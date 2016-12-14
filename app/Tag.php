<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /**
     * タグの最大文字数。
     *
     * @var int
     */
    const MAX_LENGTH = 50;
    
    /**
     * 1辞書あたりの最大タグ数。
     *
     * @var int
     */
    const MAX_TAGS = 20;
}
