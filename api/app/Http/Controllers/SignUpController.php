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

        if (!$data['name']) {
            return HttpHelpers::badRequest(new MissingParamError('name'));
        }

        return new HttpResponse();
    }
}
