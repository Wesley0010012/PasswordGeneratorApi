<?php

namespace App\Data\UseCases;

use App\Data\Protocols\CheckAccountRepository;
use App\Domain\UseCases\CheckAccount;

class DbCheckAccount implements CheckAccount
{
    public function __construct(private CheckAccountRepository $checkAccountRepository)
    {
    }

    public function verifyIfExists(string $email): bool
    {
        return !!$this->checkAccountRepository->findAccountByEmail($email);
    }
}
