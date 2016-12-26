<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExternalLoginRequest extends FormRequest
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
        return [
            'provider' => ['required', 'in:' . implode(',', config('auth.services'))],
        ];
    }
    
    /**
     * @inheritDoc
     */
    public function messages()
    {
        return [
            'required' => _('セッション切れです。もう一度お試しください。'),
        ] + parent::messages();
    }
}
