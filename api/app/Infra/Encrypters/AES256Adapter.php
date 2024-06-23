<?php

namespace App\Infra\Encrypters;

use App\Data\Protocols\Encrypter;

class AES256Adapter implements Encrypter
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
}
