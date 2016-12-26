<?php

namespace App\Http\Requests;

use URL;

class ExternalLoginCallbackRequest extends ExternalLoginRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge_recursive(parent::rules(), [
            'provider' => ['external_account'],
        ]);
    }
    
    /**
     * @inheritDoc
     */
    public function messages()
    {
        return [
            'external_account' => _('アプリ連携を許可する必要があります。'),
        ] + parent::messages();
    }
}
