<?php

namespace App\Http\Protocols;

interface TokenDecrypter
{
    public function decrypt(string $token): array | false;
}
