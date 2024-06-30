<?php

namespace App\Domain\Models;

class FindAccountModel
{
    private string $email;
    private string $password;

    public function __construct(
        string $email,
        string $password
    ) {
        $this->setEmail($email);
        $this->setPassword($password);
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}
