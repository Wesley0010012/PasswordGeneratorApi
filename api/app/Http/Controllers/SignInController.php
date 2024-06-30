<?php

namespace App\Http\Controllers;

use App\Domain\Models\FindAccountModel;
use App\Domain\UseCases\FindAccount;
use App\Exceptions\InvalidParamError;
use App\Exceptions\MissingParamError;
use App\Exceptions\UnauthenticatedError;
use App\Http\Helpers\HttpHelpers;
use App\Http\Protocols\EmailValidator;
use App\Http\Protocols\HttpRequest;
use App\Http\Protocols\HttpResponse;
use App\Http\Protocols\TokenGenerator;
use Throwable;

class SignInController extends Controller
{
    public function __construct(
        private readonly EmailValidator $emailValidator,
        private readonly FindAccount $findAccount,
        private readonly TokenGenerator $tokenGenerator
    ) {
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

            $accountModel = $this->findAccount->getAccount(new FindAccountModel($email, $password));

            if (!$accountModel) {
                return HttpHelpers::badRequest(new UnauthenticatedError($email));
            }

            return HttpHelpers::success($this->tokenGenerator->generate($accountModel));
        } catch (Throwable $e) {
            return HttpHelpers::internalServerError();
        }
    }
}
