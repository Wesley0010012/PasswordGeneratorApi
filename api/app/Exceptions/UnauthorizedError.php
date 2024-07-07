<?php

namespace App\Exceptions;

use Error;

class UnauthorizedError extends Error
{
    public function __construct(string $token)
    {
        parent::__construct('Unauthorized account with token: ' . $token);
    }
}
