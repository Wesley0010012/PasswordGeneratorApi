<?php

namespace App\Data\UseCases;

use App\Data\Protocols\AddAccountRepository;
use App\Domain\Models\AddAccountModel;
use App\Domain\Models\AccountModel;
use App\Domain\UseCases\AddAccount;

class DbAddAccount implements AddAccount
{
    public function __construct(private AddAccountRepository $addAccountRepository)
    {
    }

    public function add(AddAccountModel $addAccountModel): AccountModel
    {
        $this->addAccountRepository->add($addAccountModel);

        return new AccountModel();
    }
}
