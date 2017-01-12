<?php

namespace App;

use Illuminate\Database\Eloquent\{Model, Relations\BelongsTo};
use Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use GuzzleHttp\Exception\ClientException;
use League\OAuth1\Client\Credentials\CredentialsException;
use InvalidArgumentException;

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
     * @var (string|null)[][]
     */
    protected static $userDataList = [];
    
    /**
     * サービスの表示名を取得します。
     *
     * @param  string  $provider  OAuthサービス名。
     * @return srtring
     */
    public static function getServiceDisplayName(string $provider): string
    {
        return [
            'github'   => _('GitHub'),
            'facebook' => _('Facebook'),
            'google'   => _('Google'),
            'linkedin' => _('LinkedIn'),
            'twitter'  => _('Twitter'),
        ][$provider];
    }

    /**
     * この外部アカウントが関連付けられたユーザーを取得します。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 外部アカウントのユーザーデータを取得します。
     *
     * @param  string  $provider OAuthサービス名。
     * @return (string|null)[] 失敗した場合は空の配列を返します。
     */
    public static function acquireUserDataFromRequest(string $provider): array
    {
        if (array_key_exists($provider, static::$userDataList)) {
            return static::$userDataList[$provider];
        }
        
        try {
            $driver = Socialite::driver($provider);
            if ($provider === 'facebook') {
                $driver->fields(['name', 'link', 'email', 'picture']);
            }
            $user = $driver->user();
        } catch (InvalidStateException | InvalidArgumentException | ClientException | CredentialsException $exception) {
            return [];
        }
        
        switch ($provider) {
            case 'github':
                $link = $user->offsetGet('html_url');
                break;
            case 'facebook':
                $link = $user->offsetGet('link');
                break;
            case 'google':
                $link = $user->offsetGet('url');
                break;
            case 'linkedin':
                $link = null;
                break;
            case 'twitter':
                $link = 'https://twitter.com/' . $user->getNickname();
                break;
        }
        
        return static::$userDataList[$provider] = [
            'provider' => $provider,
            'provider_user_id' => $user->getId(),
            'name' => $provider === 'facebook' ? $user->offsetGet('name') : $user->getName(),
            'email' => $user->getEmail(),
            'avatar' => $provider === 'twitter'
                ? str_replace('http://', 'https://', $user->getAvatar())
                : $user->getAvatar(),
            'link' => $link,
        ];
    }
}
