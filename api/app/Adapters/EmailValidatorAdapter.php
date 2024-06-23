<?php

namespace App\Adapters;

use App\Http\Protocols\EmailValidator;
use Egulias\EmailValidator\EmailValidator as EguliasEmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;

class EmailValidatorAdapter implements EmailValidator
{
    public function validate(string $email): bool
    {
        return (new EguliasEmailValidator)->isValid($email, new RFCValidation());
    }
}
