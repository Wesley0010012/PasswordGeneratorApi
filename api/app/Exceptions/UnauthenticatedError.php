<?php

namespace App\Exceptions;

use Error;

class UnauthenticatedError extends Error
{
    public function __construct(string $email)
    {
        parent::__construct('Unauthenticated account with email: ' . $email);
    }
}
