<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;

/**
 * 新しいDictionary、Userが作成されたとき、対応するCategoryを作成します。
 */
class ModelCategoryCreator
{
    /**
     * モデル作成イベントのリッスン。
     *
     * @param  \App\User|\App\Dictionary  $model
     * @return void
     */
    public function created(Model $model): void
    {
        $model->forumCategory()->create(['category_id' => $model::PARENT_FORUM_CATEGORY_ID, 'enable_threads' => true]);
    }
}
