<?php

namespace App\Http\Controllers;

use App\Exceptions\MissingParamError;
use App\Exceptions\UnauthorizedError;
use App\Http\Helpers\HttpHelpers;
use App\Http\Protocols\HttpRequest;
use App\Http\Protocols\HttpResponse;
use App\Http\Protocols\TokenDecrypter;

class SavePasswordController extends Controller
{
    public function __construct(
        private readonly TokenDecrypter $tokenDecrypter
    ) {
    }

    public function handle(HttpRequest $httpRequest): HttpResponse
    {
        $body = $httpRequest->getBody();

        $requiredParams = ['token', 'email', 'password', 'domain'];

        foreach ($requiredParams as $param) {
            if (!$body[$param]) {
                return HttpHelpers::badRequest(new MissingParamError($param));
            }
        }

        [
            'token' => $token,
            'email' => $email,
            'password' => $password,
            'domain' => $domain
        ] = $body;

        $decryptedToken = $this->tokenDecrypter->decrypt($token);

        if (!$decryptedToken) {
            return HttpHelpers::badRequest(new UnauthorizedError($token));
        }

        return HttpHelpers::success('success');
    }
}
