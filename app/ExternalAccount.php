<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExternalAccount extends Model
{
    /**
     * 複数代入する属性。
     *
     * @var string[]
     */
    protected $fillable = ['user_id', 'provider', 'provider_user_id', 'name', 'email', 'avatar', 'link'];
    
    /**
     * ネイティブなタイプへキャストする属性。
     *
     * @var string[]
     */
    protected $casts = [
        'available' => 'boolean',
        'public' => 'boolean',
    ];
    
    /**
     * この外部アカウントが関連付けられたユーザーを取得します。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
