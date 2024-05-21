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
        $addAccountModel->setPassword($this->encrypter->encrypt($addAccountModel->getPassword()));

        $id = $this->addAccountRepository->add($addAccountModel);

        $accountModel = new AccountModel();
        $accountModel->setId($id);
        $accountModel->setName($addAccountModel->getName());
        $accountModel->setEmail($addAccountModel->getEmail());
        $accountModel->setPassword($addAccountModel->getPassword());

        return $accountModel;
    }
}
