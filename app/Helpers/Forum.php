<?php

namespace App\Helpers;

use App\Http\Controllers\Forum\CategoriesController;
use Riari\Forum\Models\{Category, Post, Thread};
use Riari\Forum\Frontend\Support\Forum as BaseForum;

class Forum extends BaseForum
{
    /**
     * @inheritDoc
     */
    public static function route($route, $model = null): string
    {
        // ↓ parent::route($route, $model); ====================================
        if (!starts_with($route, config('forum.routing.as'))) {
            $route = config('forum.routing.as') . $route;
        }

        $params = [];
        $append = '';

        if ($model) {
            switch (true) {
                case $model instanceof Category:
                    $params = [
                        'category'      => $model->id,
                        'category_slug' => static::slugify($model->title)
                    ];
                    break;
                case $model instanceof Thread:
                    $params = [
                        'category'      => $model->category->id,
                        'category_slug' => static::slugify($model->category->title),
                        'thread'        => $model->id,
                        'thread_slug'   => static::slugify($model->title)
                    ];
                    break;
                case $model instanceof Post:
                    $params = [
                        'category'      => $model->thread->category->id,
                        'category_slug' => static::slugify($model->thread->category->title),
                        'thread'        => $model->thread->id,
                        'thread_slug'   => static::slugify($model->thread->title)
                    ];

                    if ($route == config('forum.routing.as') . 'thread.show') {
                        // The requested route is for a thread; we need to specify the page number and append a hash for
                        // the post
                        $params['page'] = ceil($model->sequence / $model->getPerPage());
                        $append = "#post-{$model->sequence}";
                    } else {
                        // Other post routes require the post parameter
                        $params['post'] = $model->id;
                    }
                    break;
            }
        }
        // ↑ parent::route($route, $model); ====================================
        
        $relationalModel = request()->route('dictionary') ?? request()->route('user');
        $resourceNames = explode('.', str_replace(
            config('forum.routing.as'),
            ($relationalModel ? resource_name($relationalModel) : CategoriesController::SITE_FORUM_CATEGORY_TITLE)
                . '.',
            $route
        ));
        if (count($resourceNames) === 3
            && ($resourceNames[1] === 'category' && $resourceNames[2] === 'show'
                || in_array($resourceNames[1], ['thread', 'post']))
            || count($resourceNames) === 2 && in_array($resourceNames[1], ['index-new', 'mark-new'])) {
            switch ($resourceNames[1]) {
                case 'category':
                    $resourceNames[1] = 'threads';
                    $resourceNames[2] = 'index';
                    break;
                case 'thread':
                    $resourceNames[1] = 'threads';
                    break;
                case 'post':
                    $resourceNames[1] = 'threads.posts';
                    if (in_array($resourceNames[2], ['create', 'store']) && !isset($params['post'])) {
                        $params['post'] = 0;
                    }
                    break;
                case 'index-new':
                case 'mark-new':
                    $resourceNames[1] = 'threads.' . $resourceNames[1];
                    break;
            }
            $route = implode('.', $resourceNames);
            
            if ($relationalModel) {
                $params[parameter_name($relationalModel)] = $relationalModel->id;
            }
            array_forget($params, ['category', 'category_slug', 'thread_slug']);
        }
        
        // ↓ parent::route($route, $model); ====================================
        return route($route, $params) . $append;
        // ↑ parent::route($route, $model); ====================================
    }
}
