<?php

namespace App\Http\Factories;

use App\Adapters\EmailValidatorAdapter;
use App\Adapters\TokenGeneratorAdapter;
use App\Data\UseCases\DbAddAccount;
use App\Data\UseCases\DbCheckAccount;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SignUpController;
use App\Infra\Encrypters\AES256Adapter;
use App\Infra\Repository\AccountRepository;

class SignUpControllerFactory implements ControllerFactory
{
    public function make(): Controller
    {
        $emailValidator = new EmailValidatorAdapter();
        $tokenGenerator = new TokenGeneratorAdapter();
        $encrypter = new AES256Adapter();

        $accountRepository = new AccountRepository();

        $checkAccount = new DbCheckAccount($accountRepository);
        $addAccount = new DbAddAccount($accountRepository, $encrypter);

        return new SignUpController($emailValidator, $checkAccount, $addAccount, $tokenGenerator);
    }
}
