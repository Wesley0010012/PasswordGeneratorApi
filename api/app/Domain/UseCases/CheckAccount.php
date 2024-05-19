<?php

namespace App\Domain\UseCases;

interface CheckAccount
{
    public function verifyIfExists(string $email): bool;
}
