<?php

namespace App\Providers;

use App\ExternalAccount;
use Auth;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Auth\EloquentUserProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::provider('external', function ($app, array $config) {
            return new class($app['hash'], $config['model']) extends EloquentUserProvider {
                /**
                 * @inheritDoc
                 */
                public function retrieveByCredentials(array $credentials)
                {
                    if (count($credentials) === 1 && isset($credentials['provider'])) {
                        $userData = ExternalAccount::acquireUserDataFromRequest($credentials['provider']);
                        $externalAccount = ExternalAccount
                            ::where('provider', $userData['provider'])
                            ->where('provider_user_id', $userData['provider_user_id'])
                            ->first();
                        return $externalAccount ? $externalAccount->user : null;
                    } else {
                        return parent::retrieveByCredentials($credentials);
                    }
                }
                
                /**
                 * @inheritDoc
                 */
                public function validateCredentials(UserContract $user, array $credentials): bool
                {
                    return count($credentials) === 1 && isset($credentials['provider'])
                        ? true
                        : parent::validateCredentials($user, $credentials);
                }
            };
        });
    }
}
