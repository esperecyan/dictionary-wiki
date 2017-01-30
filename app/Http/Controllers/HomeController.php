<?php

namespace App\Http\Controllers;

use App\{User, ExternalAccount};
use Auth;
use Socialite;
use Illuminate\View\View;
use Illuminate\Http\{Request, RedirectResponse};
use Symfony\Component\HttpFoundation\RedirectResponse as BaseRedirectResponse;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['welcome']]);
    }

    /**
     * Show the application top page.
     *
     * @return \Illuminate\View\View
     */
    public function welcome(): View
    {
        return view('welcome');
    }

    /**
     * ユーザー設定ページを表示します。
     *
     * @return \Illuminate\View\View
     */
    protected function showEditForm(): View
    {
        return view('home.edit')->with('userCanDisconnect', $this->userCanDisconnect(Auth::user()));
    }
    
    /**
     * 連携の解除を行うことができる (有効な外部アカウントと2つ以上連携している) ユーザーであれば真を返します。
     * @param \App\User
     * @return bool
     */
    protected function userCanDisconnect(User $user): bool
    {
        return ExternalAccount::where('user_id', $user->id)->where('available', true)->count() >= 2;
    }
    
    /**
     * 外部アカウントとの連携を解除します。
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function disconnect(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $externalAccountId
            = ExternalAccount::where('provider', $request->disconnect)->where('user_id', $user->id)->value('id');
        if (!$externalAccountId || !$this->userCanDisconnect($user)) {
            throw new ValidationException();
        }
        
        $externalAccounts = $user->externalAccounts()->where('id', '<>', $externalAccountId);
        foreach (['name', 'avatar', 'email'] as $provided) {
            if ($user->{$provided . '_provider_id'} === $externalAccountId) {
                $provideds = $externalAccounts->whereNotNull($provided);
                $user->{$provided . '_provider_id'}
                    = $provideds->where('available', true)->value('id') ?: $provideds->value('id');
            }
        }
        
        $user->save();
        ExternalAccount::find($externalAccountId)->delete();
        
        return redirect('home/edit')->with('success', true);
    }
    
    /**
     * 外部アカウントから取得した情報を更新します。
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function refresh(Request $request): BaseRedirectResponse
    {
        $this->validate($request, ['provider' => 'exists:external_accounts,provider,user_id,' . Auth::user()->id]);
        $request->flash();
        return Socialite::driver($request->input('provider'))->redirect();
    }
    
    /**
     * 新しい外部アカウントと連携します。
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function connect(Request $request): BaseRedirectResponse
    {
        $this->validate($request, ['provider' => [
            'in:' . implode(',', config('auth.services')),
            'unique:external_accounts,provider,NULL,id,user_id,' . Auth::user()->id
        ]]);
        $request->flash();
        return Socialite::driver($request->input('provider'))->redirect();
    }
    
    /**
     * ユーザー設定のバリデートを行います。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateEdit(Request $request)
    {
        $rules = [
            'public' => ['array'],
            'profile' => ['nullable', 'markdown:App\\User::PROFILE_ALLOWED'],
        ];
        
        foreach (['name', 'avatar', 'email'] as $provided) {
            $rules["$provided-provider"]
                = "exists:external_accounts,provider,available,1,$provided,NOT_NULL,user_id," . Auth::user()->id;
        }
        
        $this->validate($request, $rules);
    }

    /**
     * ユーザー設定の編集要求を扱います。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request): BaseRedirectResponse
    {
        if ($request->has('disconnect')) {
            return $this->disconnect($request);
        }
        
        if ($request->has('refresh')) {
            return $this->refresh($request);
        }
        
        if ($request->has('connect')) {
            return $this->connect($request);
        }

        $this->validateEdit($request);
        
        $user = Auth::user();
        $user->profile = $request->input('profile');
        foreach (['name', 'avatar', 'email'] as $provided) {
            if ($request->has("$provided-provider")) {
                $user->{$provided . '_provider_id'} = ExternalAccount::where(
                    ['user_id' => Auth::user()->id, 'provider' => $request->input("$provided-provider")]
                )->value('id');
            }
        }
        $user->save();
        
        foreach (config('auth.services') as $service) {
            $externalAccount = ExternalAccount::where(['user_id' => Auth::user()->id, 'provider' => $service])->first();
            if ($externalAccount) {
                $externalAccount->public = in_array($service, $request->input('public', []));
                $externalAccount->save();
            }
        }
        
        return redirect('home/edit')->with('success', true);
    }
}
