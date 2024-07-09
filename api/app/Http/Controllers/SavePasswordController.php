<?php

namespace App\Http\Controllers;

use App\Exceptions\MissingParamError;
use App\Http\Helpers\HttpHelpers;
use App\Http\Protocols\HttpRequest;
use App\Http\Protocols\HttpResponse;

class SavePasswordController extends Controller
{
    public function handle(HttpRequest $httpRequest): HttpResponse
    {
        $body = $httpRequest->getBody();

        $requiredParams = ['token', 'email', 'password', 'domain'];

        foreach ($requiredParams as $param) {
            if (!$body[$param]) {
                return HttpHelpers::badRequest(new MissingParamError($param));
            }
        }

        return HttpHelpers::success('success');
    }
}
