<?php

namespace App\Http\Controllers\Forum;

use App;
use Html;
use Markdown;
use App\{Dictionary, User};
use Riari\Forum\Models\{Category, Thread, Post};
use Riari\Forum\Frontend\Http\Controllers\CategoryController;
use Roumen\Feed\Feed;
use Illuminate\Http\{Request, Response};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use esperecyan\html_filter\Filter;

class CategoriesController extends CategoryController
{
    /**
     * サイトのフィードバック欄に対応するCategoryのid。
     *
     * @var int
     */
    const SITE_FORUM_CATEGORY_ID = 3;
    
    /**
     * サイトのフィードバック欄に対応するCategoryのtitle。
     *
     * @var string
     */
    const SITE_FORUM_CATEGORY_TITLE = 'site';
    
    /**
     * サイトのフィードバック欄にスレッドが一つもない場合に、Atomのupdated要素で記述する日時。
     *
     * @var string
     */
    const SITE_FORUM_DEFAULT_ATOM_UPDATED = '2016-12-14T15:36:56+09:00';
    
    /**
     * フィードをキャッシュする秒数。
     *
     * @var int
     */
    const FEED_CACHE_LIFETIME = 60;
    
    /**
     * Dictionaryに対応するカテゴリのスレッド一覧を表示します。
     *
     * @param  \App\Dictionary  $dictionary
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function dictionariesThreadsIndex(Dictionary $dictionary, Request $request)
    {
        return $this->show($request, $dictionary);
    }
    
    /**
     * Userに対応するスレッド一覧を表示します。
     *
     * @param  \App\User  $user
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function usersThreadsIndex(User $user, Request $request)
    {
        return $this->show($request, $user);
    }
    
    /**
     * サイトのフィードバック欄に対応するカテゴリのスレッド一覧を表示します。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function siteThreadsIndex(Request $request)
    {
        return $this->show($request);
    }
    
    /**
     * スレッド一覧、またはレス一覧のフィードを表示します。
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Dictionary|\App\User|null  $model
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function show(Request $request, Model $model = null)
    {
        return $request->type === 'atom' ? $this->generateFeed($request, $model) : parent::show($request);
    }
    
    /**
     * カテゴリに所属するスレッドに所属するレス一覧のフィードを生成します。
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Dictionary|\App\User|null  $model
     * @return \Illuminate\Http\Response
     */
    protected function generateFeed(Request $request, Model $model = null): Response
    {
        $feed = new Feed();
        if (App::environment('staging', 'production')) {
            $feed->setCache(
                static::FEED_CACHE_LIFETIME,
                ($model ? parameter_name($model) . "-$model->id:" : '') . 'threads'
            );
        }
        if (!$feed->isCached()) {
            $posts = ($model->forumCategory ?? Category::find(static::SITE_FORUM_CATEGORY_ID))
                ->hasManyThrough(Post::class, Thread::class)->with('parent', 'author.nameProvider')->latest()
                ->limit((new Post())->getPerPage())->get();
            $feed->title = _('辞書まとめwiki')
                . ' — ' . ($model ? ($model->title ?? $model->name) . ' — ' . _('コメント欄') : _('サイトのフィードバック'));
            if (count($posts) > 0) {
                $feed->pubdate = $posts[0]->created_at;
            } else {
                switch (request()->route()->getName()) {
                    case 'dictionaries.threads.index':
                        $feed->pubdate = $model->revisions()->oldest()->limit(1)->get()[0]->created_at;
                        break;
                    case 'users.threads.index':
                        $feed->pubdate = $model->created_at;
                        break;
                    case static::SITE_FORUM_CATEGORY_TITLE . '.threads.index':
                        $feed->pubdate = static::SITE_FORUM_DEFAULT_ATOM_UPDATED;
                        break;
                }
            }
            $feed->description = isset($model->summary) || isset($model->profile)
                ? new HtmlString('<div xmlns="http://www.w3.org/1999/xhtml">
                    <h1></h1>' .
                    isset($model->summary)
                        ? Html::convertField($model->summary)
                        : (new Filter())->filter(Markdown::convertToHtml($model->profile))
                . '</div>')
                : null;
            $feed->link = route(
                $request->route()->getName(),
                ($model ? [parameter_name($model) => $model->id] : []) + ['type' => 'atom']
            );
            $feed->icon = url('favicon.ico');
            $feed->logo = isset($model->avatar) && $model->avatar ? $model->avatar : url('logo.png');
            $feed->lang = $model->locale ?? null;

            foreach ($posts as $post) {
                $feed->setItem([
                    'id' => 'tag:' . env('FEED_TAGGING_ENTITY') . ":dictionary-wiki:post-$post->id",
                    'title' => $post->thread->title . " — {$post->author->name}",
                    'author' => $post->author->name,
                    'authorURL' => route('users.show', ['user' => $post->author->id]),
                    'link'
                        => route($request->route()->getName(), ($model ? [parameter_name($model) => $model->id] : [])),
                    'pubdate' => $post->created_at,
                    'description' => null,
                    'content' => $post->content,
                    'enclosure' => [],
                    'category' => null,
                ]);
            }
        }

        return $feed->render('atom');
    }
}
