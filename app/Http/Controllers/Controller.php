<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Database\Eloquent\Model;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    /**
     * トランザクションを開始し、指定したモデルに対応する行に対して排他的ロックを行い、コールバック関数を実行します。
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param callable $callback
     * @return mixid
     */
    protected function transactionAndLock(Model $model, callable $callback)
    {
        return DB::transaction(function () use ($model, $callback) {
            $model->lockForUpdate()->get();
            return call_user_func($callback);
        });
    }
}
