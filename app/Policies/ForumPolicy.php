<?php

namespace App\Policies;

use Riari\Forum\Policies\ForumPolicy as BaseForumPolicy;

class ForumPolicy extends BaseForumPolicy
{
    /**
     * @inheritDoc
     */
    public function createCategories($user): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function moveCategories($user): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function renameCategories($user): bool
    {
        return false;
    }
}
