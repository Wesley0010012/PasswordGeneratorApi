<?php

namespace App\Data\UseCases;

use App\Data\Protocols\Encrypter;
use App\Domain\Models\AddPasswordModel;
use App\Domain\Models\PasswordModel;
use App\Domain\UseCases\AddPassword;

class DbAddPassword implements AddPassword
{
    public function __construct(
        private readonly Encrypter $encrypter
    ) {
    }

    public function add(AddPasswordModel $addPasswordModel): PasswordModel
    {
        $addPasswordModel->setPassword($this->encrypter->encrypt($addPasswordModel->getPassword()));

        return new PasswordModel();
    }
}
