<?php

namespace App\Http\Factories;

use App\Adapters\EmailValidatorAdapter;
use App\Adapters\TokenGeneratorAdapter;
use App\Data\UseCases\DbFindAccount;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SignInController;
use App\Infra\Encrypters\AES256Adapter;
use App\Infra\Repository\AccountRepository;

class SignInControllerFactory implements ControllerFactory
{
    public function make(): Controller
    {
        $emailValidator = new EmailValidatorAdapter();
        $tokenGenerator = new TokenGeneratorAdapter();
        $encrypter = new AES256Adapter();

        $accountRepository = new AccountRepository();

        $findAccount = new DbFindAccount($encrypter, $accountRepository);

        return new SignInController($emailValidator, $findAccount, $tokenGenerator);
    }
}
