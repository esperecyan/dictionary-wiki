<?php

namespace App\Observers;

use App\Revision;

/**
 * ユーザーの関連情報 (編集数など) を更新します。
 */
class UserInfomationCacheUpdater
{
    /**
     * リビジョン作成イベントのリッスン。
     *
     * @param  \App\Revision  $revision
     * @return void
     */
    public function created(Revision $revision): void
    {
        $user = $revision->user()->withCount('revisions')->first();
        $user->revision_count = $user->revisions_count;
        $user->revision_created_at = $revision->freshTimestamp();
        $user->timestamps = false;
        $user->save();
    }
}
