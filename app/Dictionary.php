<?php

namespace App;

use esperecyan\dictionary_php\Dictionary as DictionaryRecord;
use Storage;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\{Model, Builder, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\{HasMany, HasOne, BelongsToMany};
use Collective\Html\Eloquent\FormAccessible;
use Kyslik\ColumnSortable\Sortable;
use FilesystemIterator;

class Dictionary extends Model
{
    use SoftDeletes, HasForumCategory, Sortable, Searchable, FormAccessible;
    
    /**
     * 言語タグの最大文字数。
     *
     * @var int
     */
    const MAX_LOCALE_LENGTH = 35;
    
    /**
     * 有効なカテゴリ値。
     *
     * @var string[]
     */
    const CATEGORIES = ['generic', 'specific', 'private'];
    
    /**
     * 汎用辞書に1つのフィールドの文字数制限 (CommonMarkで記述するフィールドを除く)。
     *
     * @var int
     */
    const MAX_FIELD_LENGTH = 400;
    
    /**
     * 各モデルに対応するCategoryの親となるCategoryのid。
     *
     * @var int
     */
    const PARENT_FORUM_CATEGORY_ID = 1;
    
    /**
     * モデルのタイムスタンプを更新するかの指示。
     *
     * @var bool
     */
    public $timestamps = false;
    
    /**
     * 日付へキャストする属性。
     *
     * @var string[]
     */
    protected $dates = ['updated_at', 'deleted_at'];

    /**
     * @inheritDoc
     */
    protected $perPage = 50;

    /**
     * 更新するリレーション。
     *
     * @var string[]
     */
    protected $touches = ['tags'];
    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->locale = _('ja');
    }
    
    /**
     * @inheritDoc
     */
    public function toSearchableArray()
    {
        return array_except([
            'tags' => $this->tags->pluck('name')->toArray(),
            'recordTexts' => array_pluck($this->latest->getWords(), 'text.0'),
        ] + $this->attributesToArray(), ['latest']);
    }
    
    /**
     * Dictionary::$latest のアクセサ。
     *
     * @param string $latest
     * @return \esperecyan\dictionary_php\Dictionary
     */
    public function getLatestAttribute(string $latest): DictionaryRecord
    {
        $dictionary = unserialize($latest);
        if ($this->getRelationValue('files')->first()) {
            $dictionary->setFiles(
                new FilesystemIterator(config('filesystems.disks')[Storage::getDefaultDriver()]['root']
                    . '/' . File::DIRECTORY_NAME . "/$this->id")
            );
        }
        return $dictionary;
    }
    
    /**
     * Dictionary::$latest のミューテタ。
     *
     * @param \esperecyan\dictionary_php\Dictionary $dictionary
     * @return void
     */
    public function setLatestAttribute(DictionaryRecord $dictionary): void
    {
        $this->attributes['latest'] = serialize($dictionary);
    }
    
    /**
     * カテゴリの表示名を取得します。
     *
     * @return string
     */
    public function getCategoryNameAttribute(): string
    {
        switch ($this->category) {
            case 'generic':
                $name = _('一般・全般');
                break;
            case 'specific':
                $name = _('版権・専門');
                break;
            case 'private':
                $name = _('個人用');
                break;
        }
        return $name;
    }
    
    /**
     * 更新履歴を取得します。
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function revisions(): HasMany
    {
        return $this->hasMany(Revision::class);
    }
    
    /**
     * 最新の更新を取得します。
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function revision(): HasOne
    {
        return $this->hasOne(Revision::class)->latest();
    }
    
    /**
     * 最古の更新を取得します。
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function oldestRevision(): HasOne
    {
        return $this->hasOne(Revision::class)->oldest();
    }
    
    /**
     * 辞書内のファイルを取得します。
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }
    
    /**
     * 辞書に付いているタグを取得します。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
    
    /**
     * 辞書編集フォームにおいて、更新内容欄に summary プロパティ値 (CSVファイルの @summary フィールド値) が表示されるのを防ぎます。
     *
     * @return string
     */
    public function formSummaryAttribute(): string
    {
        return '';
    }
    
    /**
     * 辞書に付いているタグを改行 (LF) 区切りで取得します。
     *
     * @return string
     */
    public function formTagsAttribute(): string
    {
        return $this->getRelationValue('tags')->implode('name', "\n");
    }
    
    /**
     * カテゴリ「個人用」(private) が設定されていない辞書に限定するクエリスコープ。
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('category', '<>', 'private');
    }
}
