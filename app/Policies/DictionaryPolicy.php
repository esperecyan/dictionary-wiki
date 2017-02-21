<?php

namespace App\Policies;

use App\{User, Dictionary};

class DictionaryPolicy
{
    /**
     * ユーザーが対象の辞書を更新する権限 (個人用辞書かつ自身が作成した辞書) があれば、真を返します。
     *
     * @param  \App\User  $user
     * @param  \App\Dictionary  $dictionary
     * @return bool
     */
    public function update(User $user, Dictionary $dictionary): bool
    {
        return $dictionary->category !== 'private' || $user->id === $dictionary->oldestRevision->user_id;
    }
}
