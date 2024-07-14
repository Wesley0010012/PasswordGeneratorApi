<?php

namespace App\Domain\Models;

class AddPasswordModel
{
    private string $passwordAccount;
    private string $password;
    private string $domain;
    private int $accountId;

    public function __construct(
        string $passwordAccount,
        string $password,
        string $domain,
        int $accountId
    ) {
        $this->passwordAccount = $passwordAccount;
        $this->password = $password;
        $this->domain = $domain;
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

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    public function getAccountId(): int
    {
        return $this->accountId;
    }

    public function setAccountId(int $accountId): void
    {
        $this->accountId = $accountId;
    }
}
