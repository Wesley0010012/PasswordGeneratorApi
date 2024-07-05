<?php

namespace App\Adapters;

use App\Http\Protocols\TokenDecrypter;
use Throwable;

class TokenDecrypterAdapter implements TokenDecrypter
{
    public function decrypt(string $token): array|false
    {
        $decryptedToken = base64_decode($token);

        $explodedToken = @explode(',', $decryptedToken);

        if(sizeof($explodedToken) != 2) {
            return false;
        }

        return [
            'email' => $explodedToken[0],
            'password' => $explodedToken[1]
        ];
    }
}
