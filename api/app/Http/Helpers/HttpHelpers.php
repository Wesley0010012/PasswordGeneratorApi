<?php

namespace App\Http\Helpers;

use App\Exceptions\InternalServerError;
use App\Http\Protocols\HttpResponse;
use Error;

abstract class HttpHelpers
{
    public static function badRequest(Error $error): HttpResponse
    {
        $httpResponse = new HttpResponse();
        $httpResponse->setStatusCode(400);
        $httpResponse->setBody($error);

        return $httpResponse;
    }

    public static function internalServerError(): HttpResponse
    {
        $httpResponse = new HttpResponse();
        $httpResponse->setStatusCode(500);
        $httpResponse->setBody(new InternalServerError());

        return $httpResponse;
    }
}
