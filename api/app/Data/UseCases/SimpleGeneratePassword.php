<?php

namespace App\Data\UseCases;

use App\Domain\UseCases\GeneratePassword;

class SimpleGeneratePassword implements GeneratePassword
{
    public function generate(int $passwordSize): string
    {
        return substr(base64_encode(random_bytes($passwordSize)), 0, $passwordSize);
    }
}
