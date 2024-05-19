<?php

namespace App\Http\Controllers;

use App\Domain\Models\AddAccountModel;
use App\Domain\Models\FindAccountModel;
use App\Domain\UseCases\AddAccount;
use App\Domain\UseCases\CheckAccount;
use App\Exceptions\AccountExistsError;
use App\Exceptions\InvalidParamError;
use App\Exceptions\MissingParamError;
use App\Http\Helpers\HttpHelpers;
use App\Http\Protocols\EmailValidator;
use App\Http\Protocols\HttpRequest;
use App\Http\Protocols\HttpResponse;
use App\Http\Protocols\TokenGenerator;
use Throwable;

class SignUpController extends Controller
{
    public function __construct(
        private EmailValidator $emailValidator,
        private CheckAccount $checkAccount,
        private AddAccount $addAccount,
        private TokenGenerator $tokenGenerator
    ) {
    }

    public function handle(HttpRequest $httpRequest): HttpResponse
    {
        try {
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

            if ($password !== $passwordConfirmation) {
                return HttpHelpers::badRequest(new InvalidParamError('passwordConfirmation'));
            }

            if ($this->checkAccount->verifyIfExists(new FindAccountModel($name, $email, $password))) {
                return HttpHelpers::badRequest(new AccountExistsError($email));
            }

            $accountModel = $this->addAccount->add(new AddAccountModel($name, $email, $password));

            $token = $this->tokenGenerator->generate($accountModel);

            return HttpHelpers::success($token);
        } catch (Throwable $e) {
            return HttpHelpers::internalServerError();
        }
    }
}
