<?php

namespace App\Http\Controllers;

use App\{ExternalAccount, Dictionary, Revision, File as FileModel, Tag};
use App\Http\Requests\{IndexDictionariesRequest, ModifyDictionaryRequest};
use Auth;
use File;
use Storage;
use Illuminate\View\View;
use Illuminate\Http\{Request, Response, RedirectResponse, UploadedFile};
use App\Http\JsonResponse;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\{Response as BaseResponse, ResponseHeaderBag};
use esperecyan\dictionary_php\{Parser, Serializer, Dictionary as DictionaryRecord};
use esperecyan\dictionary_php\exception\{SerializeExceptionInterface, SyntaxException};
use Normalizer;
use FilesystemIterator;
use SplTempFileObject;
use Psr\Log\{LoggerInterface, LoggerTrait};

class DictionariesController extends Controller implements LoggerInterface
{
    use LoggerTrait;
    
    /** @var string[] */
    protected $messages = [];
    
    /** @inheritDoc */
    public function log($level, $message, array $context = [])
    {
        $this->messages[] = "$level: $message";
    }
    
    /**
     * Web API における type パラメータ値をキーに、esperecyan\dictionary_php\Serializer() の第1引数を値にもつ配列。最小の要素が既定値。
     *
     * @var string[]
     */
    const TYPES = [
        'csv' => '汎用辞書',
        'cfq' => 'キャッチフィーリング',
        'dat' => 'きゃっちま',
        'quiz' => 'Inteligenceω クイズ',
        'siri' => 'Inteligenceω しりとり',
        'pictsense' => 'ピクトセンス',
    ];
    
    /**
     * ResponseHeaderBag#makeDisposition() の第3引数に設定するためのダミーのファイル名。拡張子を含みません。
     *
     * @var string
     */
    const FILENAME_FALLBACK = 'dictionary';
    
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show', 'words']]);
        $this->middleware('can:update,dictionary', ['only' => ['edit', 'update']]);
    }
    
    /**
     * 辞書の一覧を表示します。
     *
     * @param \App\Http\Requests\IndexDictionariesRequest $request
     * @return \Illuminate\View\View|\App\Http\JsonResponse
     */
    public function index(IndexDictionariesRequest $request)
    {
        $dictionaries = $request->has('search')
            // Support for Laravel Scout · Issue #48 · Kyslik/column-sortable
            // <https://github.com/Kyslik/column-sortable/issues/48#issuecomment-270252558>
            ? Dictionary::whereIn('id', Dictionary::search($request->search)->get()->pluck('id'))
            : new Dictionary();
        if ($request->scope === 'without-warnings') {
            $dictionaries = $dictionaries->withoutWarnings();
        }
        $dictionaries = $dictionaries->public()
            ->sortable(['updated_at' => 'desc'])->paginate()->appends($request->except('page'));
        return $request->type === 'json'
            ? new JsonResponse($dictionaries, Response::HTTP_OK, ['access-control-allow-origin' => '*'])
            : view('dictionary.index')->with('dictionaries', $dictionaries);
    }
    
    /**
     * 辞書の作成フォームを表示します。
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request): View
    {
        return view('dictionary.create');
    }
    
    /**
     * 辞書を新規作成します。
     *
     * @param \App\Http\Requests\ModifyDictionaryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ModifyDictionaryRequest $request): RedirectResponse
    {
        $dictionary = new Dictionary();
        if ($request->input('uploading') === '1') {
            $parsedDictionary = $this->parseDictionaryFile($request);
            if (!$parsedDictionary) {
                return back(Response::HTTP_SEE_OTHER)->withInput();
            }
        } else {
            $request->validateDictionary($this);
            $parsedDictionary = null;
        }
        
        Dictionary::withoutSyncingToSearch(function () use ($dictionary) {
            $dictionary->save();
        });
        $this->transactionAndLock($dictionary, function () use ($dictionary, $request, $parsedDictionary) {
            $dictionary->category = $request->input('category');
            $dictionary->locale = $request->input('locale');
            $this->modifyDictionary($request, $dictionary, $parsedDictionary);
        });
        return redirect()->route('dictionaries.show', [$dictionary], Response::HTTP_SEE_OTHER)->with('success', true);
    }
    
    /**
     * 辞書の概要を表示します。
     *
     * @param \App\Dictionary $dictionary
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\App\Http\JsonResponse
     */
    public function show(Dictionary $dictionary, Request $request)
    {
        if ($request->type === 'json' && $request->scope === 'header') {
            return new JsonResponse(
                $dictionary->toArray() + ['tags' => $dictionary->tags->pluck('name')->toArray()],
                Response::HTTP_OK,
                ['access-control-allow-origin' => '*']
            );
        } elseif ($request->exists('type')) {
            return $this->get($dictionary, $request);
        } else {
            return view('dictionary.show')->with('dictionary', $dictionary);
        }
    }
    
    /**
     * 辞書のお題一覧を表示します。
     *
     * @param \App\Dictionary $dictionary
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function words(Dictionary $dictionary, Request $request): View
    {
        return view('dictionary.words')
            ->with(['dictionary' => $dictionary, 'records' => $dictionary->revision->records]);
    }
    
    /**
     * 辞書の更新フォームを表示します。
     *
     * @param \App\Dictionary $dictionary
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function edit(Dictionary $dictionary, Request $request): View
    {
        return view('dictionary.edit')->with('dictionary', $dictionary);
    }
    
    /**
     * 辞書を更新します。
     *
     * @param \App\Dictionary $dictionary
     * @param \App\Http\Requests\ModifyDictionaryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Dictionary $dictionary, ModifyDictionaryRequest $request): RedirectResponse
    {
        $this->transactionAndLock($dictionary, function () use ($dictionary, $request) {
            $request->validateDictionary($this, $dictionary);
            
            // ファイルの削除
            $this->deleteFiles($dictionary, array_merge(
                $request->has('deleted-file-names') ? $request->input('deleted-file-names') : [],
                array_map(function (UploadedFile $addedFile): string {
                    return $addedFile->getClientOriginalName();
                }, $request->file('added-files', []))
            ));

            $this->modifyDictionary($request, $dictionary);
        });
        return redirect()->route('dictionaries.show', [$dictionary], Response::HTTP_SEE_OTHER)->with('success', true);
    }
    
    /**
     * 入力されたタグの矯正を行います。
     *
     * @param string $tag
     * @return string 空文字になる場合もあります。
     */
    protected function correctTag(string $tag): string
    {
        return mb_substr(trim(preg_replace('/[\\p{Z}_]+/u', ' ', preg_replace('/\\p{C}/u', '', str_replace(
            ['~', '"', '\''],
            ['〜', '”', '’'],
            preg_replace('/"(.*?)"/u', '“$1”', Normalizer::normalize($tag, Normalizer::FORM_KC))
        )))), 0, Tag::MAX_LENGTH, 'UTF-8');
    }
    
    /**
     * 辞書ファイルのアップロードによる新規登録において、modifyDictionary() メソッドが利用できるように DictionaryRecord を返します。
     *
     * @param \App\Http\Requests\ModifyDictionaryRequest $request
     * @return esperecyan\dictionary_php\Dictionary|null
     */
    protected function parseDictionaryFile(ModifyDictionaryRequest $request)//: ?Dictionary
    {
        $dictionaryFile = $request->file('dictionary');
        
        $parser = new Parser(
            $request->has('type') ? static::TYPES[$request->input('type')] : null,
            $dictionaryFile->getClientOriginalName() ?? sprintf(_('%sの辞書'), Auth::user()->name),
            $request->has('name') ? $request->input('name') : null
        );
        $parser->setLogger($this);
        
        try {
            $dictionary = $parser->parse($dictionaryFile);
        } catch (SyntaxException $exception) {
            $this->messages[] = $exception->getMessage();
        }
        $request->session()->flash('errors', new MessageBag(['dictionary' => $this->messages]));
        
        return $dictionary ?? null;
    }
    
    /**
     * 辞書からファイルを削除します。
     *
     * @param \App\Dictionary $dictionary
     * @param string[] $deletedFilenames
     * @return void
     */
    protected function deleteFiles(Dictionary $dictionary, array $deletedFilenames): void
    {
        $files = $dictionary->files();
        foreach ($deletedFilenames as $deletedFilename) {
            $file = $files->where('name', $deletedFilename)->first();
            if ($file) {
                $this->deleteFile($dictionary, $file);
                $file->delete();
            }
        }
        
        if (!Storage::files(FileModel::DIRECTORY_NAME . "/$dictionary->id")) {
            Storage::deleteDirectory(FileModel::DIRECTORY_NAME . "/$dictionary->id");
        }
    }
    
    /**
     * 辞書にファイルを追加します。
     *
     * @param \App\Dictionary $dictionary
     * @param \App\Revision $revision
     * @param \Illuminate\Http\UploadedFile[] $addedFiles
     * @return void
     */
    protected function addFiles(Dictionary $dictionary, Revision $revision, array $addedFiles): void
    {
        foreach ($addedFiles as $addedFile) {
            $this->saveFile($dictionary, $addedFile);
            $file = new FileModel();
            $file->dictionary_id = $dictionary->id;
            $file->revision_id = $revision->id;
            $file->name = $addedFile->getClientOriginalName();
            $file->type = $addedFile->getMimeType();
            $file->save();
        }
    }
    
    /**
     * ストレージにファイルを保存します。
     *
     * @param \App\Dictionary $dictionary
     * @param \Illuminate\Http\UploadedFile $file
     * @return void
     */
    protected function saveFile(Dictionary $dictionary, UploadedFile $file): void
    {
        Storage::put(
            FileModel::DIRECTORY_NAME . "/$dictionary->id/" . $file->getClientOriginalName()
                . ($file->getClientOriginalExtension() === 'mp4' && $file->getMimeType() === 'audio/mp4' ? '.m4a' : ''),
            File::get($file)
        );
    }
    
    /**
     * ストレージからファイルを削除します。
     *
     * @param \App\Dictionary $dictionary
     * @param \App\File $file
     * @return void
     */
    protected function deleteFile(Dictionary $dictionary, FileModel $file): void
    {
        Storage::delete(FileModel::DIRECTORY_NAME . "/$dictionary->id/$file->name"
            . ($file->type === 'audio/mp4' && ends_with($file->name, '.mp4') ? '.m4a' : ''));
    }
    
    /**
     * 辞書についているタグの追加・削除を行います。
     *
     * @param \App\Dictionary $dictionary
     * @param string[] $tags
     * @return void
     */
    protected function modifyTags(Dictionary $dictionary, array $tags): void
    {
        $allTags = $tags ? Tag::pluck('name', 'id') : null;
        $dictionary->tags()->sync(array_map(function (string $tag) use ($allTags)/*: int*/ {
            $id = $allTags->search($tag);
            if (!$id) {
                $tagModel = new Tag();
                $tagModel->name = $tag;
                $tagModel->save();
                $id = $tagModel->id;
            }
            return $id;
        }, $tags));
    }
    
    /**
     * 入力データからタグの一覧を返します。制限を超える分は切り詰めます。
     *
     * @param \Illuminate\Http\Request $request
     * @return string[]
     */
    protected function getTagsFromInputData(Request $request): array
    {
        return $request->has('tags') ? array_slice(array_filter(
            $request->has('tags') ? array_map(
                [$this, 'correctTag'],
                preg_split('/\\R/u', $request->input('tags'), null, PREG_SPLIT_NO_EMPTY)
            ) : [],
            function (string $tag): bool {
                return $tag !== '';
            }
        ), 0, Tag::MAX_TAGS) : [];
    }
    
    /**
     * 辞書の更新、リビジョンの作成、ファイルの追加、タグの追加・削除を行います。
     *
     * あらかじめファイルの削除を行っておく必要があります。
     *
     * @param \App\Http\Requests\ModifyDictionaryRequest $request
     * @param \App\Dictionary $dictionary
     * @param \esperecyan\dictionary_php\Dictionary $parsedDictionary 新規作成時、辞書ファイルのアップロードによって作成する場合。
     * @return void
     */
    protected function modifyDictionary(
        ModifyDictionaryRequest $request,
        Dictionary $dictionary,
        DictionaryRecord $parsedDictionary = null
    ): void {
        // リビジョンの作成
        $user = Auth::user();
        $revision = new Revision();
        $revision->dictionary_id = $dictionary->id;
        $revision->user_id = $user->id;
        $revision->save();
        $revision->summary = $request->input('summary');
        $revision->ipaddr = $request->ip();
        $externalAccounts = ExternalAccount::where('user_id', $user->id);
        foreach ($externalAccounts->where('available', true)->get() ?: [$externalAccounts->first()] as $account) {
            $availableExternalAccounts[$account->provider] = $account->provider_user_id;
        }
        $revision->external_accounts = $availableExternalAccounts;
        
        // ファイルの追加
        if ($parsedDictionary) {
            $files = $parsedDictionary->getFiles();
            if ($files) {
                $files->setFlags(FilesystemIterator::KEY_AS_FILENAME | FilesystemIterator::CURRENT_AS_PATHNAME);
                foreach ($files as $filename => $path) {
                    $addedFiles[] = new UploadedFile($path, $filename);
                }
            }
        } elseif ($request->hasFile('added-files')) {
            $addedFiles = $request->file('added-files');
        }
        
        if (isset($addedFiles)) {
            $this->addFiles($dictionary, $revision, $addedFiles);
        }
        $files = $dictionary->files()->pluck('revision_id', 'name');
        $revision->files = $files;

        // タグの追加・削除
        $this->modifyTags($dictionary, $this->getTagsFromInputData($request));
        $revision->tags = $dictionary->tags()->pluck('name');
        
        // CSVファイルの取得
        if (!$parsedDictionary) {
            $file = new SplTempFileObject();
            $file->fwrite($request->input('csv'));
            $parsedDictionary = (new Parser('汎用辞書'))->parse($file, true, $files->keys()->toArray());
        }
        $dictionary->latest = $parsedDictionary;
        $revision->data = (new Serializer('汎用辞書'))->serialize($parsedDictionary, true)['bytes'];
        
        // タイトル等の取得
        $dictionary->title = $parsedDictionary->getTitle();
        $metadata = $parsedDictionary->getMetadata();
        if (isset($metadata['@summary'])) {
            $dictionary->summary = $metadata['@summary']['lml'];
        }
        if (isset($metadata['@regard'])) {
            $dictionary->regard = $metadata['@regard'];
        }
        $dictionary->words = count($parsedDictionary->getWords());
        
        $revision->save();
        $dictionary->save();
    }
    
    /**
     * 各形式に変換した辞書を返します。
     *
     * @param \App\Dictionary $dictionary
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\App\Http\JsonResponse
     */
    protected function get(Dictionary $dictionary, Request $request): BaseResponse
    {
        if (isset(static::TYPES[$request->input('type')])) {
            $latest = $dictionary->latest;

            $serializer = new Serializer(static::TYPES[$request->input('type')]);
            try {
                $file = $serializer->serialize($latest, $request->scope === 'text'
                    ? route('dictionaries.files.show', ['dictionary' => $dictionary->id, 'file' => '%s'])
                    : false);
            } catch (SerializeExceptionInterface $exception) {
                return new JsonResponse(
                    [
                        'type' => 'https://github.com/esperecyan/dictionary-api/blob/master/serialize-error.md',
                        'title' => 'Serialize Error',
                        'status' => Response::HTTP_BAD_REQUEST,
                        'detail' => $exception->getMessage(),
                    ],
                    Response::HTTP_BAD_REQUEST,
                    ['content-type' => 'application/problem+json', 'access-control-allow-origin' => '*']
                );
            }
            
            return response($file['bytes'])
                ->header('content-type', $file['type'])
                ->header('content-disposition', (new ResponseHeaderBag())->makeDisposition(
                    $request->input('type') === 'pictsense'
                        ? ResponseHeaderBag::DISPOSITION_INLINE
                        : ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                    $file['name'],
                    static::FILENAME_FALLBACK . '.' . explode('.', $file['name'])[1]
                ))
                ->header('access-control-allow-origin', '*');
        } else {
            return redirect($request->fullUrlWithQuery(['type' => array_keys(static::TYPES)[0]]));
        }
    }
}
