<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidParamError;
use App\Exceptions\MissingParamError;
use App\Http\Helpers\HttpHelpers;
use App\Http\Protocols\HttpRequest;
use App\Http\Protocols\HttpResponse;
use App\Http\Protocols\TokenDecrypter;
use Throwable;

class GeneratePasswordController extends Controller
{
    public function __construct(private readonly TokenDecrypter $tokenDecrypter)
    {
    }

    public function handle(HttpRequest $httpRequest): HttpResponse
    {
        try {
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

            $decryptedToken = $this->tokenDecrypter->decrypt($token);

            if (!$decryptedToken) {
                return HttpHelpers::badRequest(new InvalidParamError('token'));
            }

            return HttpHelpers::success("PASS");
        } catch (Throwable $e) {
            return HttpHelpers::internalServerError();
        }
    }
}
