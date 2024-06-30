<?php

namespace App\Domain\UseCases;

use App\Domain\Models\AccountModel;
use App\Domain\Models\FindAccountModel;

interface FindAccount
{
    public function getAccount(FindAccountModel $findAccountModel): ?AccountModel;
}
