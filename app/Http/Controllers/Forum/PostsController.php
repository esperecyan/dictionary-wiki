<?php

namespace App\Http\Controllers\Forum;

use App\{Dictionary, User};
use Riari\Forum\Frontend\Http\Controllers\PostController;
use Riari\Forum\Models\{Thread, Post};
use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\View\View;

class PostsController extends PostController
{
    /**
     * Dictionaryに対応するカテゴリのスレッドのレスを表示します。
     *
     * @param  \App\Dictionary  $dictionary
     * @param  \Riari\Forum\Models\Thread  $thread
     * @param  \Riari\Forum\Models\Post  $post
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function dictionariesPostsShow(Dictionary $dictionary, Thread $thread, Post $post, Request $request): View
    {
        return parent::show($request);
    }
    
    /**
     * Userに対応するカテゴリのスレッドのレスを表示します。
     *
     * @param  \App\User  $user
     * @param  \Riari\Forum\Models\Thread  $thread
     * @param  \Riari\Forum\Models\Post  $post
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function usersPostsShow(User $user, Thread $thread, Post $post, Request $request): View
    {
        return parent::show($request);
    }
    
    /**
     * サイトのフィードバック欄に対応するカテゴリのスレッドのレスを表示します。
     *
     * @param  \Riari\Forum\Models\Thread  $thread
     * @param  \Riari\Forum\Models\Post  $post
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function sitePostsShow(Thread $thread, Post $post, Request $request): View
    {
        return parent::show($request);
    }
    
    /**
     * Dictionaryに対応するカテゴリのスレッドへの返信フォームを表示します。
     *
     * @param  \App\Dictionary  $dictionary
     * @param  \Riari\Forum\Models\Thread  $thread
     * @param  \Illuminate\Http\Request  $request
     * @param  \Riari\Forum\Models\Post|null  $post
     * @return \Illuminate\View\View
     */
    public function dictionariesPostsCreate(
        Dictionary $dictionary,
        Thread $thread,
        Request $request,
        Post $post = null
    ): View {
        return parent::create($request);
    }
    
    /**
     * Userに対応するカテゴリのスレッドへの返信フォームを表示します。
     *
     * @param  \App\User  $user
     * @param  \Riari\Forum\Models\Thread  $thread
     * @param  \Illuminate\Http\Request  $request
     * @param  \Riari\Forum\Models\Post|null  $post
     * @return \Illuminate\View\View
     */
    public function usersPostsCreate(User $user, Thread $thread, Request $request, Post $post = null): View
    {
        return parent::create($request);
    }
    
    /**
     * サイトのフィードバック欄に対応するカテゴリのスレッドへの返信フォームを表示します。
     *
     * @param  \Riari\Forum\Models\Thread  $thread
     * @param  \Illuminate\Http\Request  $request
     * @param  \Riari\Forum\Models\Post|null  $post
     * @return \Illuminate\View\View
     */
    public function sitePostsCreate(Thread $thread, Request $request, Post $post = null): View
    {
        return parent::create($request);
    }
    
    /**
     * Dictionaryに対応するカテゴリのスレッドに返信します。
     *
     * @param  \App\Dictionary  $dictionary
     * @param  \Riari\Forum\Models\Thread  $thread
     * @param  \Illuminate\Http\Request  $request
     * @param  \Riari\Forum\Models\Post|null  $post
     * @return Illuminate\Http\RedirectResponse
     */
    public function dictionariesPostsStore(
        Dictionary $dictionary,
        Thread $thread,
        Request $request,
        Post $post = null
    ): RedirectResponse {
        return parent::store($request);
    }
    
    /**
     * Userに対応するカテゴリのスレッドに返信します。
     *
     * @param  \App\User  $user
     * @param  \Riari\Forum\Models\Thread  $thread
     * @param  \Illuminate\Http\Request  $request
     * @param  \Riari\Forum\Models\Post|nulls  $post
     * @return Illuminate\Http\RedirectResponse
     */
    public function usersPostsStore(User $user, Thread $thread, Request $request, Post $post = null): RedirectResponse
    {
        return parent::store($request);
    }
    
    /**
     * サイトのフィードバック欄に対応するカテゴリのスレッドに返信します。
     *
     * @param  \Riari\Forum\Models\Thread  $thread
     * @param  \Illuminate\Http\Request  $request
     * @param  \Riari\Forum\Models\Post|null  $post
     * @return Illuminate\Http\RedirectResponse
     */
    public function sitePostsStore(Thread $thread, Request $request, Post $post = null): RedirectResponse
    {
        return parent::store($request);
    }
}
