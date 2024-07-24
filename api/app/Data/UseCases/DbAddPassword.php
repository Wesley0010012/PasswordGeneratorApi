<?php

namespace App\Data\UseCases;

use App\Data\Protocols\AddPasswordRepository;
use App\Data\Protocols\Encrypter;
use App\Domain\Models\AddPasswordModel;
use App\Domain\Models\PasswordModel;
use App\Domain\UseCases\AddPassword;

class DbAddPassword implements AddPassword
{
    public function __construct(
        private readonly Encrypter $encrypter,
        private readonly AddPasswordRepository $addPasswordRepository
    ) {
    }

    public function add(AddPasswordModel $addPasswordModel): PasswordModel
    {
        $addPasswordModel->setPassword($this->encrypter->encrypt($addPasswordModel->getPassword()));

        $passwordModel = new PasswordModel();
        $passwordModel->setPasswordAccount($addPasswordModel->getPasswordAccount());
        $passwordModel->setPassword($addPasswordModel->getPassword());
        $passwordModel->setDomain($addPasswordModel->getDomain());
        $passwordModel->setAccountId($addPasswordModel->getAccountId());

        $passwordModel->setId($this->addPasswordRepository->add($passwordModel));

        return $passwordModel;
    }
}
