<?php

return [

    /*
    |--------------------------------------------------------------------------
    | taggingEntity
    |--------------------------------------------------------------------------
    |
    | フィードのatom:id要素の内容として使うtagスキームのURL生成に利用する、
    | wiki毎に一意のtaggingEntity。
    |
    | 例: example.com,2016
    |
    | 参照: <https://tools.ietf.org/html/rfc4151#section-2.1>
    |
    */

    'taggingEntity' => env('FEED_TAGGING_ENTITY'),

];
