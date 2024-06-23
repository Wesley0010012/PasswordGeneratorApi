<?php

namespace App\Data\Protocols;

use App\Domain\Models\AccountModel;

interface AddAccountRepository
{
    public function add(AccountModel $accountModel): int;
}
