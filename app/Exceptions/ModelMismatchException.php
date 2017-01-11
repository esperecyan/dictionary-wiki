<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * モデル結合ルートにおいて、複数のモデルが親子関係にない場合に投げられる例外。
 */
class ModelMismatchException extends ModelNotFoundException
{
    /**
     * @inheritDoc
     */
    public function setModel($model, $ids = []): ModelMismatchException
    {
        parent::setModel($model, $ids);
        $this->message = "子モデル [{$model}] は親モデルに属していません。";
        return $this;
    }
}
