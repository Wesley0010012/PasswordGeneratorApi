<?php

namespace App\Http\Controllers;

use App\Domain\Models\AddPasswordModel;
use App\Domain\Models\FindAccountModel;
use App\Domain\Models\FindPasswordModel;
use App\Domain\UseCases\AddPassword;
use App\Domain\UseCases\FindAccount;
use App\Domain\UseCases\CheckPassword;
use App\Exceptions\InvalidParamError;
use App\Exceptions\MissingParamError;
use App\Exceptions\PasswordAccountExistsError;
use App\Exceptions\UnauthorizedError;
use App\Http\Helpers\HttpHelpers;
use App\Http\Protocols\HttpRequest;
use App\Http\Protocols\HttpResponse;
use App\Http\Protocols\TokenDecrypter;
use Throwable;

class SavePasswordController extends Controller
{
    public function __construct(
        private readonly TokenDecrypter $tokenDecrypter,
        private readonly FindAccount $findAccount,
        private readonly CheckPassword $checkPassword,
        private readonly AddPassword $addPassword
    ) {
    }

    public function handle(HttpRequest $httpRequest): HttpResponse
    {
        try {
            $body = $httpRequest->getBody();

            $requiredParams = ['token', 'account', 'password', 'domain'];

            foreach ($requiredParams as $param) {
                if (!$body[$param]) {
                    return HttpHelpers::badRequest(new MissingParamError($param));
                }
            }

            [
                'token' => $token,
                'account' => $account,
                'password' => $password,
                'domain' => $domain
            ] = $body;

            $decryptedToken = $this->tokenDecrypter->decrypt($token);

            if (!$decryptedToken) {
                return HttpHelpers::badRequest(new InvalidParamError($token));
            }

            [
                'email' => $email,
                'password' => $passwordToken
            ] = $decryptedToken;

            $accountModel = $this->findAccount->getAccount(new FindAccountModel($email, $passwordToken));

            if (!$accountModel) {
                return HttpHelpers::badRequest(new UnauthorizedError($token));
            }

            if ($this->checkPassword->check(new FindPasswordModel($accountModel->getId(), $account, $domain))) {
                return HttpHelpers::badRequest(new PasswordAccountExistsError($account, $domain));
            }

            $this->addPassword->add(new AddPasswordModel($account, $password, $domain, $accountModel->getId()));

            return HttpHelpers::success([
                "success" => true,
                "message" => "password added with success"
            ]);
        } catch (Throwable $e) {
            return HttpHelpers::internalServerError();
        }
    }
}
