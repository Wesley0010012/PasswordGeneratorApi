<?php

namespace App\Exceptions;

use Error;

class InternalServerError extends Error
{
    public function __construct()
    {
        parent::__construct('internal server error');
    }
}
