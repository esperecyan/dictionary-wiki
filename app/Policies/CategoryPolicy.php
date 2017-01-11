<?php

namespace App\Policies;

use Riari\Forum\{Models\Category, Policies\CategoryPolicy as BaseCategoryPolicy};

class CategoryPolicy extends BaseCategoryPolicy
{
    /**
     * @inheritDoc
     */
    public function deleteThreads($user, Category $category): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function enableThreads($user, Category $category): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function moveThreadsFrom($user, Category $category): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function moveThreadsTo($user, Category $category): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function lockThreads($user, Category $category): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function pinThreads($user, Category $category): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function delete($user, Category $category): bool
    {
        return false;
    }
}
