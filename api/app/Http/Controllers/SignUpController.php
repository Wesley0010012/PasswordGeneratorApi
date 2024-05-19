<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidParamError;
use App\Exceptions\MissingParamError;
use App\Http\Helpers\HttpHelpers;
use App\Http\Protocols\EmailValidator;
use App\Http\Protocols\HttpRequest;
use App\Http\Protocols\HttpResponse;

class SignUpController extends Controller
{
    public function __construct(private EmailValidator $emailValidator)
    {
    }

    public function handle(HttpRequest $httpRequest): HttpResponse
    {
        $data = $httpRequest->getBody();

        $params = ['name', 'email', 'password', 'passwordConfirmation'];

        foreach ($params as $param) {
            if (!$data[$param]) {
                return HttpHelpers::badRequest(new MissingParamError($param));
            }
        }

        [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'passwordConfirmation' => $passwordConfirmation
        ] = $data;

        if (!$this->emailValidator->validate($email)) {
            return HttpHelpers::badRequest(new InvalidParamError('email'));
        }

        return new HttpResponse();
    }
}
