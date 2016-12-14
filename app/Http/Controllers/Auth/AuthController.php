<?php

namespace App\Http\Controllers\Auth;

use App\{User, ExternalAccount};
use Auth;
use Validator;
use Socialite;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\{Request, RedirectResponse};
use Symfony\Component\HttpFoundation\{Response as BaseResponse, RedirectResponse as BaseRedirectResponse};
use Laravel\Socialite\Two\InvalidStateException;
use GuzzleHttp\Exception\ClientException;
use InvalidArgumentException;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request): void
    {
        $this->validate($request, [
            'provider' => ['required', 'in:' . implode(',', config('auth.services'))],
        ]);
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function login(Request $request): BaseRedirectResponse
    {
        $this->validateLogin($request);
        $request->session()->flash('provider', $request->input('provider'));
        $request->session()->flash('remember', $request->has('remember'));
        return Socialite::driver(session('provider'))->redirect();
    }

    /**
     * ユーザーの情報をOAuthサービスから取得し、情報をもとにログインします。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback(Request $request): BaseResponse
    {
        if ($request->session()->has('provider')) {
            $externalAccountUserData = $this->getExternalAccountUserData(session('provider'));
            if ($request->session()->has('action')) {
                $user = Auth::user();
                if ($externalAccountUserData && ExternalAccount::where([
                    'provider' => session('provider'),
                    'provider_user_id' => $externalAccountUserData['provider_user_id'],
                    ['user_id', '<>', $user->id]
                ])->exists()) {
                    return redirect('home/edit')->withErrors(_('該当の外部アカウントはすでに他のユーザーと連携しています。'));
                }
                switch (session('action')) {
                    case 'refresh':
                        // 更新
                        return $this->refresh($externalAccountUserData);
                    case 'connect':
                        // 連携
                        return $this->connect($externalAccountUserData);
                }
            } else {
                if ($externalAccountUserData) {
                    $externalAccount = ExternalAccount
                        ::where('provider', session('provider'))
                        ->where('provider_user_id', $externalAccountUserData['provider_user_id'])
                        ->first();
                    if ($externalAccount) {
                        // ログイン
                        Auth::guard($this->getGuard())->login($externalAccount->user, session('remember'));
                        return $this->handleUserWasAuthenticated($request, false);
                    } else {
                        // 新規登録
                        return $this->regist($externalAccountUserData);
                    }
                }
                return $this->sendFailedLoginMessage($request, _('ログインするには、アプリ連携を許可する必要があります。'));
            }
        }
        
        return $this->sendFailedLoginMessage($request, _('セッション切れです。もう一度お試しください。'));
    }
    
    /**
     * 外部アカウントから取得した情報を更新します。
     *
     * @param  (string|null)[]  $externalAccountUserData
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function refresh(array $externalAccountUserData): RedirectResponse
    {
        $user = Auth::user();
        $externalAccount
            = ExternalAccount::where('user_id', $user->id)->where('provider', session('provider'))->first();
        if ($externalAccountUserData) {
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
        
        $externalAccount->available = false;
        $externalAccount->save();
        
        $externalAccounts = $user->externalAccounts()->where('id', '<>', $externalAccount->id);
        foreach (['name', 'avatar', 'email'] as $provided) {
            if ($user->{$provided . '_provider_id'} === $externalAccount->id) {
                $notNullProviderId
                    = $externalAccounts->whereNotNull($provided)->where('available', true)->value('id');
                if ($notNullProviderId) {
                    $user->{$provided . '_provider_id'} = $notNullProviderId;
                }
            }
        }
        $user->save();
        
        return redirect('home/edit')->withErrors(_('アプリ連携が拒否されました。'));
    }
    
    /**
     * 新しい外部アカウントと連携します。
     *
     * @param  (string|null)[]  $externalAccountUserData
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function connect(array $externalAccountUserData): RedirectResponse
    {
        $user = Auth::user();
        if ($externalAccountUserData) {
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
        return redirect('home/edit')->withErrors(_('アプリ連携が拒否されました。'));
    }
    
    /**
     * ログインページにリダイレクトし、エラーメッセージを表示します。
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  (string|null)[]  $errorMessage
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginMessage(Request $request, string $errorMessage): RedirectResponse
    {
        return $this->sendFailedLoginResponse($request)->setTargetUrl(url('login'))->withErrors($errorMessage);
    }
    
    /**
     * ユーザーを登録します。
     *
     * @param  (string|null)[]  $externalAccountUserData
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function regist(array $externalAccountUserData): RedirectResponse
    {
        Auth::guard($this->getGuard())->login($this->create($externalAccountUserData), session('remember'));
        return redirect($this->redirectPath());
    }

    /**
     * 外部アカウントのユーザーデータを取得します。
     *
     * @param  string  $provider OAuthサービス名。
     * @return (string|null)[]
     */
    protected function getExternalAccountUserData(string $provider): array
    {
        try {
            $driver = Socialite::driver(session('provider'));
        } catch (InvalidStateException $exception) {
            return null;
        }
        
        if (session('provider') === 'facebook') {
            $driver->fields(['name', 'link', 'email', 'picture']);
        }
        
        try {
            $user = $driver->user();
        } catch (InvalidArgumentException $exception) {
            return [];
        } catch (ClientException $exception) {
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
        return [
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
    
    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  (string|null)[]  $externalAccountUserData
     * @return User
     */
    protected function create(array $externalAccountUserData): User
    {
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
