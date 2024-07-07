<?php

namespace App\Infra\Encrypters;

use App\Data\Protocols\Decrypter;
use App\Data\Protocols\Encrypter;

class AES256Adapter implements
    Encrypter,
    Decrypter
{
    private string $key;
    private string $initializationVector;
    private const ENC_ALGORITHM = 'AES-256-CBC';

    public function __construct()
    {
        $this->key = getenv("APP_KEY");
        $this->initializationVector = hex2bin(getenv("VECTOR_KEY"));
    }

    public function encrypt(string $plaintext): string
    {
        return base64_encode(openssl_encrypt($plaintext, self::ENC_ALGORITHM, $this->key, OPENSSL_RAW_DATA, $this->initializationVector));
    }

    public function decrypt(string $cyphertext): string
    {
        return openssl_decrypt(base64_decode($cyphertext), self::ENC_ALGORITHM, $this->key, OPENSSL_RAW_DATA, $this->initializationVector);
    }
}
