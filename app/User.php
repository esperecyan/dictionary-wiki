<?php

namespace App;

use esperecyan\webidl\TypeError;
use esperecyan\url\URL;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\{HasMany, BelongsTo};
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes, HasForumCategory, Sortable;
    
    /** プロフィールの最大文字数。
     *
     * @var int
     */
    const MAX_PROFILE_LENGTH = 10000;
    
    /**
     * プロフィールで許可される要素・属性。
     *
     * @var (string|(string|callable|string[])[])[]
     */
    const PROFILE_ALLOWED = [
        '*' => [
            'dir' => ['ltr', 'rtl', 'auto'],
            'lang' => '/^[a-z]+(-[0-9a-z]+)*$/iu',
            'title',
            'translate' => ['', 'yes', 'no'],
        ],
        'a' => ['href' => self::class . '::isAbsoluteURLWithHTTPScheme'], 'abbr', 'b', 'bdi', 'bdo',
        'blockquote' => ['cite' => self::class . '::isAbsoluteURLWithHTTPScheme'],
        'br', 'caption', 'cite', 'code',
        'col' => ['span' => '/^[1-9][0-9]*$/u'], 'colgroup' => ['span' => '/^[1-9][0-9]*$/u'], 'dd',
        'del' => ['cite' => self::class . '::isAbsoluteURLWithHTTPScheme', 'datetime'],
        'dfn', 'dl', 'dt', 'em', 'figcaption', 'figure', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'hr', 'i',
        'ins' => ['cite' => self::class . '::isAbsoluteURLWithHTTPScheme', 'datetime'], 'kbd', 'li',
        'ol' => ['reversed' => [''], 'start' => '/^(?:0|-?[1-9][0-9]*)$/u', 'type' => ['1', 'A', 'a', 'i', 'I']],
        'p', 'pre', 'q' => ['cite' => self::class . '::isAbsoluteURLWithHTTPScheme'],
        'rp', 'rt', 'ruby', 's', 'samp', 'small', 'strong', 'sub', 'sup', 'table', 'tbody',
        'td' => ['colspan' => '/^[1-9][0-9]*$/u', 'rowspan' => '/^[1-9][0-9]*$/u'], 'tfoot',
        'th' => [
            'abbr', 'colspan' => '/^[1-9][0-9]*$/u', 'rowspan' => '/^[1-9][0-9]*$/u',
            'scope' => ['row', 'col', 'rowgroup', 'colgroup'],
        ],
        'thead', 'time' => 'datetime', 'tr', 'u', 'ul', 'var', 'wbr',
    ];
    
    /**
     * 各モデルに対応するCategoryの親となるCategoryのid。
     *
     * @var int
     */
    const PARENT_FORUM_CATEGORY_ID = 2;
    
    /**
     * 日付へキャストする属性。
     *
     * @var string[]
     */
    protected $dates = ['revision_created_at', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @inheritDoc
     */
    protected $perPage = 50;
    
    /**
     * HTTP(S)スキームをもつ絶対URLであれば真を返します。
     *
     * @param string $value
     * @return bool
     */
    public static function isAbsoluteURLWithHTTPScheme(string $value): bool
    {
        try {
            $url = new URL($value);
        } catch (TypeError $exception) {
            return false;
        }
        return in_array($url->protocol, ['http:', 'https:']);
    }
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    /**
     * 関連付けられた外部アカウントを取得します。
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function externalAccounts(): HasMany
    {
        return $this->hasMany(ExternalAccount::class);
    }
    
    /**
     * 作成したリビジョンを取得します。
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function revisions(): HasMany
    {
        return $this->hasMany(Revision::class);
    }
    
    /**
     * 名前を提供する外部アカウントを取得します。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function nameProvider(): BelongsTo
    {
        return $this->belongsTo(ExternalAccount::class, 'name_provider_id');
    }
    
    /**
     * kyslik/column-sortable用の nameProvider() メソッドの別名。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->nameProvider();
    }
    
    /**
     * 外部アカウントから名前を取得します。
     *
     * @return string
     */
    public function getNameAttribute(): string
    {
        return $this->relationLoaded('externalAccounts')
            ? $this->getRelation('externalAccounts')->keyBy('id')[$this->name_provider_id]['name']
            : $this->getRelationValue('nameProvider')->name;
    }
    
    /**
     * メールアドレスを提供する外部アカウントを取得します。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function emailProvider(): BelongsTo
    {
        return $this->belongsTo(ExternalAccount::class, 'email_provider_id');
    }
    
    /**
     * 外部アカウントからメールアドレスを取得します。
     *
     * @return string メールアドレスが存在しない場合は、空文字列を返します。
     */
    public function getEmailAttribute(): string
    {
        return $this->relationLoaded('externalAccounts')
            ? $this->getRelation('externalAccounts')->keyBy('id')[$this->email_provider_id]['email'] ?? ''
            : $this->getRelationValue('emailProvider')->email ?? '';
    }
    
    /**
     * アバターを提供する外部アカウントを取得します。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function avatarProvider(): BelongsTo
    {
        return $this->belongsTo(ExternalAccount::class, 'avatar_provider_id');
    }
    
    /**
     * 外部アカウントからアバターを取得します。
     *
     * @return string アバターが存在しない場合、また有効なアカウントのアバターでない場合は、空文字列を返します。
     */
    public function getAvatarAttribute(): string
    {
        $avatarProvider = ($this->relationLoaded('externalAccounts')
            ? $this->getRelation('externalAccounts')->keyBy('id')[$this->avatar_provider_id] ?? null
            : $this->getRelationValue('avatarProvider')) ?? null;
        return $avatarProvider && $avatarProvider['available'] ? $avatarProvider['avatar'] : '';
    }
    
    /**
     * 公開されている外部アカウントのリンクを取得します。
     *
     * @return \Illuminate\Support\Collection キーにプロバイダを表す文字列を持ちます。
     */
    public function getLinksAttribute(): Collection
    {
        return $this->getRelationValue('externalAccounts')
            ->where('available', true)->where('link', true)->where('public', true)
            ->pluck('link', 'provider');
    }
}
