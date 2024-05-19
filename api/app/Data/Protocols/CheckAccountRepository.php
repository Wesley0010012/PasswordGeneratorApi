<?php

namespace App\Data\Protocols;

use App\Domain\Models\AccountModel;

interface CheckAccountRepository
{
    public function findAccountByEmail(string $email): AccountModel|null;
}
