<?php

namespace App\Exceptions;

use Error;

class InvalidParamError extends Error
{
    public function __construct(string $param)
    {
        parent::__construct('Invalid param: ' . $param);
    }
}
