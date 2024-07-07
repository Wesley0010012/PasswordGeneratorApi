<?php

namespace App\Data\Protocols;

interface Decrypter
{
    public function decrypt(string $cyphertext): string;
}
