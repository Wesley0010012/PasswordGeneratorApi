<?php

namespace App\Adapters;

use App\Domain\Models\AccountModel;
use App\Http\Protocols\TokenGenerator;

class TokenGeneratorAdapter implements TokenGenerator
{
    public function generate(AccountModel $accountModel): string
    {
        return json_encode([
            "account" => $accountModel->getName(),
            "email" => $accountModel->getEmail(),
            "password" => $accountModel->getPassword()
        ]);
    }
}
