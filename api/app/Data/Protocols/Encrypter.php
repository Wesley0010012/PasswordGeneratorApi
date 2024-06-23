<?php

namespace App\Data\Protocols;

interface Encrypter
{
    public function encrypt(string $plaintext): string;
}
