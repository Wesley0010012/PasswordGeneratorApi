<?php

namespace App\Http\Protocols;

interface EmailValidator
{
    public function validate(string $email): bool;
}
