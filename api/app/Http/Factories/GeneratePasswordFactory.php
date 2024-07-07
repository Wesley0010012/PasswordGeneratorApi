<?php

namespace App\Http\Factories;

use App\Adapters\TokenDecrypterAdapter;
use App\Data\UseCases\DbFindAccount;
use App\Data\UseCases\SimpleGeneratePassword;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneratePasswordController;
use App\Infra\Encrypters\AES256Adapter;
use App\Infra\Repository\AccountRepository;

class GeneratePasswordFactory implements ControllerFactory
{
    public function make(): Controller
    {
        $tokenDecrypter = new TokenDecrypterAdapter();

        $encrypterAndDecrypter = new AES256Adapter();

        $findAccountRepository = new AccountRepository();

        $findAccount = new DbFindAccount($encrypterAndDecrypter, $findAccountRepository);

        $generatePassword = new SimpleGeneratePassword();

        return new GeneratePasswordController(
            $tokenDecrypter,
            $findAccount,
            $encrypterAndDecrypter,
            $generatePassword
        );
    }
}
