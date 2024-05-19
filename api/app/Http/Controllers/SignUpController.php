<?php

namespace App\Http\Controllers;

use App\Exceptions\MissingParamError;
use App\Http\Helpers\HttpHelpers;
use App\Http\Protocols\HttpRequest;
use App\Http\Protocols\HttpResponse;

class SignUpController extends Controller
{
    public function handle(HttpRequest $httpRequest): HttpResponse
    {
        $data = $httpRequest->getBody();

        $params = ['name', 'email', 'password', 'passwordConfirmation'];

        foreach ($params as $param) {
            if (!$data[$param]) {
                return HttpHelpers::badRequest(new MissingParamError($param));
            }
        }

        return new HttpResponse();
    }
}
