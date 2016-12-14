<?php

namespace App;

use Markdown;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use esperecyan\html_filter\Filter;
use League\HTMLToMarkdown\HtmlConverter;
use Masterminds\HTML5;
use coopy_PhpTableView;
use coopy_Coopy;
use coopy_TableDiff;
use coopy_CompareFlags;
use SplTempFileObject;
use DOMElement;
use DOMText;

class Revision extends Model
{
    /** @var int 編集の要約の最大文字数。 */
    const MAX_SUMMARY_LENGTH = 500;
    
    /**
     * @var int 最後の32bitをIPv4アドレス形式で表記した場合のIPv6アドレスの最大長。
     */
    const MAX_IPADDR_LENGTH = 45;
    
    /**
     * 値がCommonMarkであるフィールドの名前 (メタフィールドを除く)。
     *
     * @see https://github.com/esperecyan/dictionary/blob/master/dictionary.md#各フィールドの構造
     * @var string[]
     */
    const MARKEDUP_FIELD_NAMES = ['image-source', 'audio-source', 'video-source', 'description'];
    
    /**
     * モデルのタイムスタンプを更新するかの指示。
     *
     * @var bool
     */
    public $timestamps = false;
    
    /**
     * 日付を変形する属性。
     *
     * @var string[]
     */
    protected $dates = ['created_at'];
    
    /**
     * ネイティブなタイプへキャストする属性。
     *
     * @var string[]
     */
    protected $casts = [
        'tags' => 'array',
        'files' => 'array',
        'external_accounts' => 'array',
    ];
    
    /**
     * 2つの二次元配列を比較し、差分情報を含む二次元配列を返します。
     *
     * 最初の配列はヘッダとして扱います。
     *
     * @param string[][] $old
     * @param string[][] $new
     * @return string[][]
     */
    public static function diffTables(array $old, array $new): array
    {
        $diffData = [];
        $diffTable = new coopy_PhpTableView($diffData);
        (new coopy_TableDiff(coopy_Coopy::compareTables(...array_map(function (array $data): coopy_PhpTableView {
            $fieldNameCounter = [];
            foreach ($data[0] as &$fieldName) {
                if (empty($fieldNameCounter[$fieldName])) {
                    $fieldNameCounter[$fieldName] = 0;
                }
                $fieldName .= '[' . ++$fieldNameCounter[$fieldName] . ']';
            }
            return new coopy_PhpTableView($data);
        }, [$old, $new]))->align(), new coopy_CompareFlags()))->hilite($diffTable);
        
        foreach ($diffData as &$fields) {
            if ($fields[0] === '@@') {
                foreach ($fields as &$fieldName) {
                    $fieldName = preg_replace('/\\[[0-9]+\\]$/u', '', $fieldName);
                }
                break;
            }
        }
        return $diffData;
    }
    
    /**
     * このリビジョンの辞書を取得します。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dictionary(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class);
    }
    
    /**
     * このリビジョンを作成したユーザーを取得します。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * PHPがWindowsでコンパイルされていれば真を返します。
     *
     * @return bool
     */
    protected function isWindows(): bool
    {
        return strpos(PHP_OS, 'WIN') === 0;
    }
    
    /**
     * CSVを単純な二次元配列に変換して取得します。
     *
     * @return string[][]
     */
    public function getCsvAttribute(): array
    {
        $file = new SplTempFileObject();
        $file->fwrite($this->data);
        $file->setFlags(SplTempFileObject::READ_AHEAD | SplTempFileObject::SKIP_EMPTY | SplTempFileObject::READ_CSV);
        
        if ($this->isWindows()) {
            $previousLocale = setlocale(LC_CTYPE, '0');
            setlocale(LC_CTYPE, '.1252');
        }
        
        $csv = iterator_to_array($file);
            
        if (isset($previousLocale)) {
            setlocale(LC_CTYPE, $previousLocale);
        }
        
        // 改行をLFに変更
        foreach ($csv as &$record) {
            foreach ($record as &$field) {
                $field = str_replace("\r\n", "\n", $field);
            }
        }
        
        return $csv;
    }
    
    /**
     * CSVを二次元配列に変換して取得します。メタフィールドは除外します。
     *
     * @return (string|\Illuminate\Support\HtmlString|null)[][]
     */
    public function getRecordsAttribute(): array
    {
        $file = new SplTempFileObject();
        $file->fwrite($this->data);
        $file->setFlags(SplTempFileObject::READ_AHEAD | SplTempFileObject::SKIP_EMPTY | SplTempFileObject::READ_CSV);
        
        if ($this->isWindows()) {
            $previousLocale = setlocale(LC_CTYPE, '0');
            setlocale(LC_CTYPE, '.1252');
        }
        
        foreach ($file as $fields) {
            $record = [];
            foreach ($fields as $i => $field) {
                if (isset($fieldNames)) {
                    // レコード
                    if ($fieldNames[$i][0] !== '@') {
                        if ($field === '') {
                            $record[] = null;
                        } elseif (in_array($fieldNames[$i], static::MARKEDUP_FIELD_NAMES)) {
                            $record[] = new HtmlString((new Filter(
                                null,
                                ['before' => function (DOMElement $body) {
                                    foreach (array_merge(...array_map(function (string $elementName) use ($body) {
                                        return iterator_to_array($body->getElementsByTagName($elementName));
                                    }, ['img', 'audio', 'video'])) as $embededContent) {
                                        $src = $embededContent->getAttribute('src');
                                        if (str_contains($src, '/')) {
                                            $embededContent->parentNode->replaceChild(
                                                new DOMText((new HtmlConverter())->convert((new HTML5())
                                                    ->saveHTML($embededContent))),
                                                $embededContent
                                            );
                                        } else {
                                            $embededContent->setAttribute('src', route('dictionaries.files.show', [
                                                'dictionary' => $this->getRelationValue('dictionary')->id,
                                                'file' => $src,
                                            ]));
                                        }
                                    }
                                }]
                            ))->filter(Markdown::convertToHtml($field)));
                        } elseif (in_array($fieldNames[$i], ['image', 'audio', 'video']) && !str_contains($field, '/')) {
                            $fileURL = route(
                                'dictionaries.files.show',
                                ['dictionary' => $this->getRelationValue('dictionary')->id, 'file' => $field]
                            );
                            switch ($fieldNames[$i]) {
                                case 'image':
                                    $record[] = new HtmlString('<img src="' . e($fileURL) . '" />');
                                    break;
                                case 'audio':
                                    $record[] = new HtmlString('<audio src="' . e($fileURL) . '" controls=""></audio>');
                                    break;
                                case 'video':
                                    $record[] = new HtmlString('<video src="' . e($fileURL) . '" controls=""></video>');
                                    break;
                            }
                        } else {
                            $record[] = $field;
                        }
                    }
                } else {
                    // ヘッダ行
                    if ($field[0] !== '@') {
                        $record[] = $field;
                    }
                }
            }
            
            if (empty($fieldNames)) {
                $fieldNames = $fields;
            }
            
            $records[] = $record;
        }
            
        if (isset($previousLocale)) {
            setlocale(LC_CTYPE, $previousLocale);
        }
        
        return $records;
    }
}
