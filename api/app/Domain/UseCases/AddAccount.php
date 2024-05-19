<?php

namespace App\Domain\UseCases;

use App\Domain\Models\AccountModel;
use App\Domain\Models\AddAccountModel;

interface AddAccount
{
    public function add(AddAccountModel $addAccountModel): AccountModel;
}
