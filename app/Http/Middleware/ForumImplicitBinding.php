<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Forum\CategoriesController;
use App\Exceptions\ModelMismatchException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Riari\Forum\Models\Category;

class ForumImplicitBinding
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        $this->setCategoryParameter($request);
        $this->helpRouteModelBinding($request);
        return $next($request);
    }

    /**
     * ルートにcategoryパラメータを設定しておきます。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \App\Exceptions\ModelMismatchException
     */
    protected function setCategoryParameter(Request $request): void
    {
        if (!$request->route()->hasParameter('category')) {
            $model = $request->route('dictionary') ?? $request->route('user');
            $request->route()->setParameter(
                'category',
                $model ? $model->forumCategory : Category::find(CategoriesController::SITE_FORUM_CATEGORY_ID)
            );
        }
    }

    /**
     * モデル結合ルートの親子関係を確認します。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \App\Exceptions\ModelMismatchException
     */
    protected function helpRouteModelBinding(Request $request): void
    {
        $thread = $request->route('thread');
        if (!$thread) {
            return;
        }
        $post = $request->route('post');
        $model = $request->route('dictionary') ?? $request->route('user');
        
        $category = $thread->category;
        $parentCategory = $category->parent;
        if ($post && $post->thread_id !== $thread->id
            || $model && $category->title != $model->id
            || ($model
                ? !$parentCategory || $parentCategory->title !== parameter_name($model)
                : $parentCategory)) {
            throw (new ModelMismatchException())->setModel($model);
        }
    }
}
