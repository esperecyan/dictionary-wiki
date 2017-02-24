<?php

namespace App\Http\Controllers;

use App\{User, Dictionary};
use App\Http\Requests\IndexUsersRequest;
use Illuminate\{Http\Request, View\View};
use App\Http\JsonResponse;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UsersController extends Controller
{
    /**
     * ユーザーの一覧を表示します。
     *
     * @param \App\Http\Requests\IndexUsersRequest $request
     * @return \Illuminate\View\View
     */
    public function index(IndexUsersRequest $request): View
    {
        return view('user.index')->with(
            'users',
            User::with('externalAccounts')
                ->sortable(['revision_count' => 'desc'])->paginate()->appends($request->except('page'))
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
    
    /**
     * ユーザーが作成した個人用辞書一覧を表示します。
     *
     * @param \App\User $user
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\App\Http\JsonResponse
     */
    public function dictionariesIndex(User $user, Request $request)
    {
        $dictionaries = Dictionary::whereIn('id', Dictionary::where('category', 'private')->with('oldestRevision')
            ->get()->where('oldestRevision.user_id', $user->id)->pluck('id'))
            ->sortable(['updated_at' => 'desc'])->paginate()->appends($request->except('page'));
        return $request->type === 'json'
            ? new JsonResponse($dictionaries)
            : view('user.dictionaries-index')->with(['shownUser' => $user, 'dictionaries' => $dictionaries]);
    }
}
