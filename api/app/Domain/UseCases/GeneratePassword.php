<?php

namespace App\Domain\UseCases;

interface GeneratePassword
{
    public function generate(int $passwordSize): string;
}
