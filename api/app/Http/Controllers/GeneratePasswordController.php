<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidParamError;
use App\Exceptions\MissingParamError;
use App\Http\Helpers\HttpHelpers;
use App\Http\Protocols\HttpRequest;
use App\Http\Protocols\HttpResponse;

class GeneratePasswordController extends Controller
{
    public function handle(HttpRequest $httpRequest): HttpResponse
    {
        $body = $httpRequest->getBody();

        $params = ['token', 'size'];

        foreach ($params as $param) {
            if (!$body[$param]) {
                return HttpHelpers::badRequest(new MissingParamError($param));
            }
        }

        ['token' => $token, 'size' => $size] = $body;

        if ($size < 1) {
            return HttpHelpers::badRequest(new InvalidParamError('size'));
        }

        return HttpHelpers::success("PASS");
    }
}
