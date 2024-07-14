<?php

namespace App\Exceptions;

use Error;

class PasswordAccountExistsError extends Error
{
    public function __construct(string $account, string $domain)
    {
        parent::__construct('password account exists: ' . $account . ' and domain: ' . $domain);
    }
}
