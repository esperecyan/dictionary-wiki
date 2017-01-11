<?php

namespace App\Http\Controllers\Forum;

use App\{Dictionary, User};
use Riari\Forum\Frontend\Http\Controllers\ThreadController;
use Riari\Forum\Models\Thread;
use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\View\View;

class ThreadsController extends ThreadController
{
    /**
     * Dictionaryに対応するカテゴリのスレッドを表示します。
     *
     * @param  \App\Dictionary  $dictionary
     * @param  \Riari\Forum\Models\Thread  $thread
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function dictionariesThreadsShow(Dictionary $dictionary, Thread $thread, Request $request): View
    {
        return parent::show($request);
    }
    
    /**
     * Userに対応するカテゴリのスレッドを表示します。
     *
     * @param  \App\User  $user
     * @param  \Riari\Forum\Models\Thread  $thread
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function usersThreadsShow(User $user, Thread $thread, Request $request): View
    {
        return parent::show($request);
    }
    
    /**
     * サイトのフィードバック欄に対応するカテゴリのスレッドを表示します。
     *
     * @param  \Riari\Forum\Models\Thread  $thread
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function siteThreadsShow(Thread $thread, Request $request): View
    {
        return parent::show($request);
    }
    
    /**
     * Dictionaryに対応するカテゴリのスレッド作成フォームを表示します。
     *
     * @param  \App\Dictionary  $dictionary
     * @param  \Riari\Forum\Models\Thread  $thread
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function dictionariesThreadsCreate(Dictionary $dictionary, Thread $thread, Request $request): View
    {
        return parent::create($request);
    }
    
    /**
     * Userに対応するカテゴリのスレッド作成フォームを表示します。
     *
     * @param  \App\User  $user
     * @param  \Riari\Forum\Models\Thread  $thread
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function usersThreadsCreate(User $user, Thread $thread, Request $request): View
    {
        return parent::create($request);
    }
    
    /**
     * サイトのフィードバック欄に対応するカテゴリのスレッド作成フォームを表示します。
     *
     * @param  \Riari\Forum\Models\Thread  $thread
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function siteThreadsCreate(Thread $thread, Request $request): View
    {
        return parent::create($request);
    }
    
    /**
     * Dictionaryに対応するカテゴリのスレッドを作成します。
     *
     * @param  \App\Dictionary  $dictionary
     * @param  \Riari\Forum\Models\Thread  $thread
     * @param  \Illuminate\Http\Request  $request
     * @return Illuminate\Http\RedirectResponse
     */
    public function dictionariesThreadsStore(Dictionary $dictionary, Thread $thread, Request $request): RedirectResponse
    {
        return parent::store($request);
    }
    
    /**
     * Userに対応するカテゴリのスレッドを作成します。
     *
     * @param  \App\User  $user
     * @param  \Riari\Forum\Models\Thread  $thread
     * @param  \Illuminate\Http\Request  $request
     * @return Illuminate\Http\RedirectResponse
     */
    public function usersThreadsStore(User $user, Thread $thread, Request $request): RedirectResponse
    {
        return parent::store($request);
    }
    
    /**
     * サイトのフィードバック欄に対応するカテゴリのスレッドを作成します。
     *
     * @param  \Riari\Forum\Models\Thread  $thread
     * @param  \Illuminate\Http\Request  $request
     * @return Illuminate\Http\RedirectResponse
     */
    public function siteThreadsStore(Thread $thread, Request $request): RedirectResponse
    {
        return parent::store($request);
    }
}
