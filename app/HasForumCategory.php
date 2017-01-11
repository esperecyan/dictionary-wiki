<?php

namespace App;

use Riari\Forum\Models\Category;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasForumCategory
{
    /**
     * 各モデルに対応するCategoryの親となるCategoryを取得します。
     *
     * @return Category
     */
    public static function getParentForumCategory(): Category
    {
        return Category::find(static::PARENT_FORUM_CATEGORY_ID);
    }
    
    /**
     * 対応するCategoryを取得します。
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function forumCategory(): HasOne
    {
        return new HasOne(
            Category::where('category_id', static::PARENT_FORUM_CATEGORY_ID),
            $this,
            'title',
            $this->getKeyName()
        );
    }
}
