<?php

namespace App\Adapters;

use App\Http\Protocols\TokenDecrypter;
use Throwable;

class TokenDecrypterAdapter implements TokenDecrypter
{
    public function decrypt(string $token): array|false
    {
        $decryptedToken = base64_decode($token);

        if (!strstr('email', $decryptedToken) || !strstr('password', $decryptedToken)) {
            return false;
        }

        $explodedToken = explode(',', $decryptedToken);

        return [
            'email' => $explodedToken[0],
            'password' => $explodedToken[1]
        ];
    }
}
