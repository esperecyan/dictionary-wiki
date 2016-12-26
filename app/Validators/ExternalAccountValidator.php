<?php

namespace App\Validators;

use App\ExternalAccount;
use Illuminate\Validation\Validator as ValidatorInstance;

class ExternalAccountValidator
{
    /**
     * 外部アカウントでアプリ連携が許可されていれば真を返します。
     *
     * @param string $attribute
     * @param string $value
     * @param string[] $parameters
     * @param \Illuminate\Validation\Validator $validator
     * @return bool
     */
    public function validate(string $attribute, string $value, array $parameters, ValidatorInstance $validator): bool
    {
        return (bool)ExternalAccount::acquireUserDataFromRequest($value);
    }
}
