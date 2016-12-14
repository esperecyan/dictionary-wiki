<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class DiffRevisionRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'revisions' => ['array', 'size:2', 'required'],
            'revisions.*' => ['exists:revisions,id'],
        ];
    }
}
