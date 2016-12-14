<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\{Http\Request, View\View};
use Illuminate\Database\Eloquent\Relations\HasMany;

class UsersController extends Controller
{
    /**
     * ユーザーの一覧を表示します。
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        return view('user.index')->with(
            'users',
            User::with(['externalAccounts', 'revisions' => function (HasMany $query): void {
                $query->orderBy('created_at', 'DESC');
            }])->get()
        );
    }
    
    /**
     * ユーザーの公開情報を表示します。
     *
     * @param \App\User $user
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function show(User $user, Request $request): View
    {
        return view('user.show')->with('shownUser', $user);
    }
}
