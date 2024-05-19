<?php

namespace App\Http\Controllers;

use App\Http\Protocols\HttpRequest;
use App\Http\Protocols\HttpResponse;

class SignUpController extends Controller
{
    public function handle(HttpRequest $httpRequest): HttpResponse
    {
        $data = $httpRequest->getBody();

        if (!$data['name']) {
            $httpResponse = new HttpResponse();
            $httpResponse->setStatusCode(400);
            $httpResponse->setBody('missing param: name');

            return $httpResponse;
        }

        return new HttpResponse();
    }
}
