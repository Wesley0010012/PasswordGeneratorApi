<?php

namespace App\Data\UseCases;

use App\Data\Protocols\Encrypter;
use App\Data\Protocols\FindAccountRepository;
use App\Domain\Models\FindAccountModel;
use App\Domain\Models\AccountModel;
use App\Domain\UseCases\FindAccount;

class DbFindAccount implements FindAccount
{
    public function __construct(
        private readonly Encrypter $encrypter,
        private readonly FindAccountRepository $findAccountRepository
    ) {
    }

    public function getAccount(FindAccountModel $findAccountModel): ?AccountModel
    {
        $findAccountModel->setPassword($this->encrypter->encrypt($findAccountModel->getPassword()));

        return $this->findAccountRepository->findAccountData($findAccountModel);
    }
}
