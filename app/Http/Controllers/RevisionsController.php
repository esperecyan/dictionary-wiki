<?php

namespace App\Http\Controllers;

use App\{Dictionary, Revision};
use App\Http\Requests\DiffRevisionRequest;
use App;
use Html;
use Roumen\Feed\Feed;
use Illuminate\Support\HtmlString;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class RevisionsController extends Controller
{
    /**
     * フィードをキャッシュする秒数。
     *
     * @var int
     */
    const FEED_CACHE_LIFETIME = 60;
    
    /**
     * 更新履歴を表示します。
     *
     * @param  \App\Dictionary  $dictionary
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function index(Dictionary $dictionary, Request $request)
    {
        return $request->type === 'atom' ? $this->generateFeed($dictionary, $request) : view('revision.index')->with([
            'dictionary' => $dictionary,
            'revisions' => $dictionary->revisions()->with('user.externalAccounts')->latest()
                ->paginate()->appends($request->except('page')),
        ]);
    }
    
    /**
     * フィードを生成します。
     *
     * @param  \App\Dictionary  $dictionary
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function generateFeed(Dictionary $dictionary, Request $request): Response
    {
        $feed = new Feed();
        if (App::environment('staging', 'production')) {
            $feed->setCache(static::FEED_CACHE_LIFETIME, $dictionary->id);
        }
        if (!$feed->isCached()) {
            $revisions = $dictionary->revisions()->with('user.nameProvider')->latest()
                ->limit((new Revision())->getPerPage())->get();
            $feed->title = _('辞書まとめwiki') . ' — ' . $dictionary->title . ' — ' . _('更新履歴');
            $feed->pubdate = $revisions[0]->created_at;
            $feed->description = isset($dictionary->summary)
                ? new HtmlString('<div xmlns="http://www.w3.org/1999/xhtml">
                    <h1></h1>' .
                    Html::convertField($dictionary->summary)
                . '</div>')
                : null;
            $feed->link = route($request->route()->getName(), ['dictionary' => $dictionary->id, 'type' => 'atom']);
            $feed->icon = url('favicon.ico');
            $feed->logo = url('logo.png');
            $feed->lang = $dictionary->locale;

            foreach ($revisions as $i => $revision) {
                $feed->setItem([
                    'id' => 'tag:' . config('feed.taggingEntity') . ":dictionary-wiki:revision-$revision->id",
                    'title' => $revision->summary,
                    'author' => $revision->user->name,
                    'authorURL' => route('users.show', ['user' => $revision->user->id]),
                    'link' => isset($revisions[$i + 1])
                        ? route('dictionaries.revisions.diff', ['dictionary' => $dictionary->id])
                            . "?revisions%5B%5D=$revision->id&revisions%5B%5D={$revisions[$i + 1]->id}"
                        : $feed->link,
                    'pubdate' => $revision->created_at,
                    'description' => null,
                    'content' => null,
                    'enclosure' => [],
                    'category' => null,
                ]);
            }
        }

        return $feed->render('atom');
    }
    
    /**
     * 更新後の辞書の内容を表示します。
     *
     * @param \App\Dictionary $dictionary
     * @param \App\Revision $revision
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function show(Dictionary $dictionary, Revision $revision, Request $request): BaseResponse
    {
        return $revision->dictionary_id === $dictionary->id
            ? response($revision->data)->header('content-type', 'text/csv; header=present; charset=UTF-8')
            : redirect()->route(
                'dictionaries.show',
                ['dictionary' => $revision->dictionary_id, 'revision' => $revision->id],
                Response::HTTP_MOVED_PERMANENTLY
            );
    }
    
    /**
     * 更新後の辞書の内容を表示します。
     *
     * @param \App\Dictionary $dictionary
     * @param \App\Revision $revisionA
     * @param \App\Revision $revisionB
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function diff(Dictionary $dictionary, DiffRevisionRequest $request)
    {
        $revisionIds = $request->input('revisions');
        sort($revisionIds, SORT_NUMERIC);
        $revisions = array_map(function (string $id): Revision {
            return Revision::find($id);
        }, $revisionIds);
        
        foreach ($revisions as $revision) {
            if ($revision->dictionary_id !== $dictionary->id) {
                return redirect()
                    ->route('dictionaries.show', ['dictionary' => $dictionary->id], Response::HTTP_SEE_OTHER)
                    ->with('_status', Response::HTTP_BAD_REQUEST);
            }
        }
        
        $oldTags = $revisions[0]->tags;
        $newTags = $revisions[1]->tags;
        $tags = [
            'added' => array_diff($newTags, $oldTags),
            'deleted' => array_diff($oldTags, $newTags),
        ];
        
        $oldFiles = $revisions[0]->files;
        $newFiles = $revisions[1]->files;
        $files = [
            'added' => array_keys(array_diff_key($newFiles, $oldFiles)),
            'deleted' => array_keys(array_diff_key($oldFiles, $newFiles)),
            'modified' => array_keys(
                array_diff(array_intersect_key($oldFiles, $newFiles), array_intersect_key($newFiles, $oldFiles))
            ),
        ];
        
        return view('revision.diff')->with([
            'dictionary' => $dictionary,
            'revisions' => $revisions,
            'tags' => array_flatten($tags) ? $tags : null,
            'files' => array_flatten($files) ? $files : null,
        ]);
    }
}
