<?php

namespace App\Data\UseCases;

use App\Data\Protocols\Encrypter;
use App\Domain\Models\FindAccountModel;
use App\Domain\Models\AccountModel;
use App\Domain\UseCases\FindAccount;

class DbFindAccount implements FindAccount
{
    public function __construct(
        private readonly Encrypter $encrypter
    ) {
    }

    public function getAccount(FindAccountModel $findAccountModel): ?AccountModel
    {
        $findAccountModel->setPassword($this->encrypter->encrypt($findAccountModel->getPassword()));

        return null;
    }
}
