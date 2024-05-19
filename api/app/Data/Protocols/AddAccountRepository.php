<?php

namespace App\Data\Protocols;

use App\Domain\Models\AddAccountModel;

interface AddAccountRepository
{
    public function add(AddAccountModel $addAccountModel): int;
}
