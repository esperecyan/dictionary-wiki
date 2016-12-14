<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{
    /**
     * 指定したキーのバリデーションルールに、必須であること (required) を追加します。
     *
     * @prama (string|string[])[] $rules
     * @param string[] $keys
     * @return string[][]
     */
    public function requireRules(array $rules, array $keys): array
    {
        return array_merge_recursive($rules, array_fill_keys($keys, 'required'));
    }
}
