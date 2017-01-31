<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    /**
     * トランザクションを開始し、指定したモデルに対応する行に対して排他的ロックを行い、コールバック関数を実行します。
     *
     * @param \Illuminate\Database\Eloquent\Model $model Searchableトレイトが追加されていれば、
     *      コールバック関数を Searchable::withoutSyncingToSearch() に渡し、トランザクション終了後に Searchable::searchable() を実行します。
     * @param callable $callback
     * @return mixid
     */
    protected function transactionAndLock(Model $model, callable $callback)
    {
        $searchable = isset(class_uses($model)[Searchable::class]);
        $result = DB::transaction(function () use ($model, $callback, $searchable) {
            $model->lockForUpdate()->get();
            return $searchable ? $model->withoutSyncingToSearch($callback) : call_user_func($callback);
        });
        if ($searchable) {
            $model->fresh()->searchable();
        }
        return $result;
    }
}
