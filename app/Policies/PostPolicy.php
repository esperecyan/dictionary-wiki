<?php

namespace App\Policies;

use Gate;
use Riari\Forum\{Models\Post, Policies\PostPolicy as BasePostPolicy};

class PostPolicy extends BasePostPolicy
{
    /**
     * @inheritDoc
     */
    public function edit($user, Post $post): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function delete($user, Post $post): bool
    {
        return Gate::forUser($user)->allows('deletePosts', $post->thread);
    }
}
