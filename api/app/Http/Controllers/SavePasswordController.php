<?php

namespace App\Http\Controllers;

use App\Domain\Models\FindAccountModel;
use App\Domain\UseCases\FindAccount;
use App\Exceptions\InvalidParamError;
use App\Exceptions\MissingParamError;
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
        private readonly FindAccount $findAccount
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
                'password' => $password
            ] = $decryptedToken;

            $account = $this->findAccount->getAccount(new FindAccountModel($email, $password));

            if (!$account) {
                return HttpHelpers::badRequest(new UnauthorizedError($token));
            }

            return HttpHelpers::success('success');
        } catch (Throwable $e) {
            return HttpHelpers::internalServerError();
        }
    }
}
