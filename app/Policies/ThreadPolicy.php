<?php

namespace App\Policies;

use Gate;
use Riari\Forum\{Models\Thread, Policies\ThreadPolicy as BaseThreadPolicy};

class ThreadPolicy extends BaseThreadPolicy
{
    /**
     * @inheritDoc
     */
    public function deletePosts($user, Thread $thread): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function rename($user, Thread $thread): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function delete($user, Thread $thread): bool
    {
        return Gate::allows('deleteThreads', $thread->category);
    }
}
