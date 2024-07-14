<?php

namespace App\Domain\Models;

class FindPasswordModel
{
    private int $accountId;
    private string $passwordAccount;
    private string $domainAccount;

    public function __construct(
        int $accountId,
        string $passwordAccount,
        string $domainAccount
    ) {
        $this->accountId = $accountId;
        $this->passwordAccount = $passwordAccount;
        $this->domainAccount = $domainAccount;
    }

    public function getAccountId(): int
    {
        return $this->accountId;
    }

    public function setAccountId(int $accountId): void
    {
        $this->accountId = $accountId;
    }

    public function getPasswordAccount(): string
    {
        return $this->passwordAccount;
    }

    public function setPasswordAccount(string $passwordAccount): void
    {
        $this->passwordAccount = $passwordAccount;
    }

    public function getDomainAccount(): string
    {
        return $this->domainAccount;
    }

    public function setDomainAccount(string $domainAccount): void
    {
        $this->domainAccount = $domainAccount;
    }
}
