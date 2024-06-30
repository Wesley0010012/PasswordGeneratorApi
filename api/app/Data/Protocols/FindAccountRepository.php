<?php

namespace App\Data\Protocols;

use App\Domain\Models\AccountModel;
use App\Domain\Models\FindAccountModel;

interface FindAccountRepository
{
    public function findAccountData(FindAccountModel $findAccountModel): ?AccountModel;
}
