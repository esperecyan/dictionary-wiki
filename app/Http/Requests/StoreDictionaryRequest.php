<?php

namespace App\Http\Requests;

use App\Http\Requests\ModifyDictionaryRequest;
use App\Dictionary;

/**
 * 辞書作成のリクエスト。
 */
class StoreDictionaryRequest extends ModifyDictionaryRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge_recursive(parent::rules(), [
            'category' => ['required', 'in:' . implode(',', Dictionary::CATEGORIES)],
            'locale' => [
                'require',
                'string',
                'max:' . Dictionary::MAX_LOCALE_LENGTH,
                'regex:/^[0-9A-Za-z]{1,8}(-[0-9A-Za-z]{1,8})*$/u',
            ],
        ]);
    }
}
