<?php

namespace App\Exceptions;

use Error;

class AccountExistsError extends Error
{
    public function __construct(string $email)
    {
        parent::__construct('email exists with email: ' . $email);
    }
}
