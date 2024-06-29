<?php

namespace App\Adapters;

use App\Domain\Models\AccountModel;
use App\Http\Protocols\TokenGenerator;

class TokenGeneratorAdapter implements TokenGenerator
{
    public function generate(AccountModel $accountModel): array
    {
        return [
            "token" => base64_encode($accountModel->getEmail() . ',' . $accountModel->getPassword())
        ];
    }
}
