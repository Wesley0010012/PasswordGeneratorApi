<?php

namespace App\Http\Protocols;

use App\Domain\Models\AccountModel;

interface TokenGenerator
{
    public function generate(AccountModel $accountModel): array;
}
