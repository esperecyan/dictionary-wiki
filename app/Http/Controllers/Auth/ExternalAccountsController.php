<?php

namespace App\Http\Controllers\Auth;

use App\{User, ExternalAccount};
use App\Http\Controllers\Controller;
use App\Http\Requests\{ExternalLoginRequest, ExternalLoginCallbackRequest};
use Auth;
use Route;
use Validator;
use Socialite;
use Illuminate\Http\{Request, RedirectResponse};
use Symfony\Component\HttpFoundation\RedirectResponse as BaseRedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\{RegistersUsers, AuthenticatesUsers};
use Illuminate\Contracts\Validation\Validator as ValidatorInterface;

class ExternalAccountsController extends Controller
{
    use RegistersUsers, AuthenticatesUsers {
        RegistersUsers::guard insteadof AuthenticatesUsers;
        RegistersUsers::redirectPath insteadof AuthenticatesUsers;
    }

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';
    
    /**
     * 外部アカウントへのアクセス許可を要求します。
     *
     * @param  \App\Http\Requests\ExternalLoginRequest  $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function requestAuthorization(ExternalLoginRequest $request): BaseRedirectResponse
    {
        session()->flash('previousRoute', Route::current()->getName());
        $request->flash();
        return Socialite::driver($request->input('provider'))->redirect();
    }
    
    /**
     * ユーザーの情報をOAuthサービスから取得し、情報をもとにログインします。
     *
     * @param  \App\Http\Requests\ExternalLoginCallbackRequest  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback(ExternalLoginCallbackRequest $request): RedirectResponse
    {
        if (starts_with(session('previousRoute'), 'users.external-accounts.')) {
            $userId = Auth::user()->id;
            $externalAccountUserData = ExternalAccount::acquireUserDataFromRequest($request->input('provider'));
            Validator::make($externalAccountUserData, [
                'provider_user_id' => [Rule::unique('external_accounts')
                    ->where('provider', $externalAccountUserData['provider'])
                    ->ignore($userId, 'user_id')],
            ], [
                'unique' => _('指定された外部アカウントはすでに他のユーザーと連携しています。'),
            ])->validate();
            switch (session('previousRoute')) {
                case 'users.external-accounts.update':
                    // 更新
                    return $this->update($externalAccountUserData);
                case 'users.external-accounts.store':
                    // 連携
                    return $this->store($externalAccountUserData);
            }
        } else {
            switch (session('previousRoute')) {
                case 'users.login':
                    // ログイン
                    return $this->login($request);
                case 'users.store':
                    // ユーザー登録
                    return $this->register($request);
            }
        }
        
        return redirect()->back()->withInput()->withErrors(_('セッション切れです。もう一度お試しください。'));
    }
    
    /**
     * 外部アカウントから取得した情報を更新します。
     *
     * @param  (string|null)[]  $externalAccountUserData
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function update(array $externalAccountUserData): RedirectResponse
    {
        $user = Auth::user();
        $externalAccount = ExternalAccount
            ::where('user_id', $user->id)->where('provider', $externalAccountUserData['provider'])->first();
        foreach (['name', 'avatar', 'email'] as $provided) {
            if (is_null($externalAccountUserData[$provided])) {
                if ($user->{$provided . '_provider_id'} === $externalAccount->id) {
                    $provideds = $user->externalAccounts()
                        ->where('id', '<>', $externalAccount->id)->whereNotNull($provided);
                    $user->{$provided . '_provider_id'}
                        = $user->$provideds->where('available', true)->value('id') ?: $provideds->value('id');
                }
            } else {
                if (is_null($user->{$provided . '_provider_id'})
                    || !ExternalAccount::find($user->{$provided . '_provider_id'})->value('available')) {
                    $user->{$provided . '_provider_id'} = $externalAccount->id;
                }
            }
        }
        $externalAccount->update($externalAccountUserData);
        $user->save();
        return redirect('home/edit')->with('success', true);
    }
    
    /**
     * 新しい外部アカウントと連携します。
     *
     * @param  (string|null)[]  $externalAccountUserData
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function store(array $externalAccountUserData): RedirectResponse
    {
        $user = Auth::user();
        $externalAccount = new ExternalAccount($externalAccountUserData);
        $externalAccount->user_id = $user->id;
        $externalAccount->save();
        foreach (['name', 'avatar', 'email'] as $provided) {
            if (!is_null($externalAccountUserData[$provided])
                && (is_null($user->{$provided . '_provider_id'})
                    || !ExternalAccount::find($user->{$provided . '_provider_id'})->value('available'))) {
                $user->{$provided . '_provider_id'} = $externalAccount->id;
            }
        }
        $user->save();
        return redirect('home/edit')->with('success', true);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request): void
    {
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string[]
     */
    protected function credentials(Request $request): array
    {
        return $request->only('provider');
    }
    
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  string[]  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data): ValidatorInterface
    {
        return Validator::make(ExternalAccount::acquireUserDataFromRequest($data['provider']), [
            'provider_user_id' => Rule::unique('external_accounts')->where('provider', $data['provider']),
        ], [
            'unique' => _('指定された外部アカウントはすでに登録されています。'),
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  string[]  $data
     * @return \App\User
     */
    protected function create(array $data): User
    {
        $externalAccountUserData = ExternalAccount::acquireUserDataFromRequest($data['provider']);
        
        $user = User::create();
        
        $externalAccount = new ExternalAccount();
        $externalAccount->user_id = $user->id;
        $externalAccount->provider = $externalAccountUserData['provider'];
        $externalAccount->provider_user_id = $externalAccountUserData['provider_user_id'];
        $externalAccount->name = $externalAccountUserData['name'];
        $externalAccount->email = $externalAccountUserData['email'];
        $externalAccount->avatar = $externalAccountUserData['avatar'];
        $externalAccount->link = $externalAccountUserData['link'];
        $externalAccount->save();
        
        $user->name_provider_id = $externalAccount->id;
        if ($externalAccount->email) {
            $user->email_provider_id = $externalAccount->id;
        }
        if ($externalAccount->avatar) {
            $user->avatar_provider_id = $externalAccount->id;
        }
        $user->save();
        
        return $user;
    }
}
