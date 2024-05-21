<?php

namespace App\Data\UseCases;

use App\Data\Protocols\AddAccountRepository;
use App\Data\Protocols\Encrypter;
use App\Domain\Models\AddAccountModel;
use App\Domain\Models\AccountModel;
use App\Domain\UseCases\AddAccount;

class DbAddAccount implements AddAccount
{
    public function __construct(
        private AddAccountRepository $addAccountRepository,
        private Encrypter $encrypter
    ) {
    }

    public function add(AddAccountModel $addAccountModel): AccountModel
    {
        $accountModel = new AccountModel();
        $accountModel->setName($addAccountModel->getName());
        $accountModel->setEmail($addAccountModel->getEmail());
        $accountModel->setPassword($this->encrypter->encrypt($addAccountModel->getPassword()));

        $id = $this->addAccountRepository->add($accountModel);

        $accountModel->setId($id);

        return $accountModel;
    }
}
