<?php

namespace App\Observers;

use App\{User, Revision};

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
        $user = $revision->user;
        $user->revision_created_at = $revision->freshTimestamp();
        $user->timestamps = false;
        $this->setRevisionCount($user);
    }
    
    /**
     * ユーザーの (個人用辞書を除く) 辞書編集数を更新します。
     *
     * @param  \App\User  $user
     * @return void
     */
    public function setRevisionCount(User $user): void
    {
        $user->load('revisions.dictionary');
        $user->revision_count = count($user->revisions->filter(function (Revision $revision): bool {
            return $revision->dictionary && $revision->dictionary->category !== 'private';
        }));
        $user->timestamps = false;
        $user->save();
    }
}
