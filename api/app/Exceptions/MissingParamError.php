<?php

namespace App\Exceptions;

use Error;

class MissingParamError extends Error
{
    public function __construct(string $param)
    {
        parent::__construct('missing param: ' . $param);
    }
}
