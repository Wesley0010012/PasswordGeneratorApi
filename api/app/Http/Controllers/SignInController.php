<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidParamError;
use App\Exceptions\MissingParamError;
use App\Http\Helpers\HttpHelpers;
use App\Http\Protocols\EmailValidator;
use App\Http\Protocols\HttpRequest;
use App\Http\Protocols\HttpResponse;
use Throwable;

class SignInController extends Controller
{
    public function __construct(private readonly EmailValidator $emailValidator)
    {
    }

    public function handle(HttpRequest $httpRequest): HttpResponse
    {
        try {
            $data = $httpRequest->getBody();

            $params = ['email', 'password'];

            foreach ($params as $param) {
                if (!$data[$param]) {
                    return HttpHelpers::badRequest(new MissingParamError($param));
                }
            }

            ['email' => $email, 'password' => $password] = $data;

            if (!$this->emailValidator->validate($email)) {
                return HttpHelpers::badRequest(new InvalidParamError('email'));
            }

            return HttpHelpers::success("ANY_SUCCESS");
        } catch (Throwable $e) {
            return HttpHelpers::internalServerError();
        }
    }
}
