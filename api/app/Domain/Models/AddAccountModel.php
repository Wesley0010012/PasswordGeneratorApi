<?php

namespace App\Domain\Models;

class AddAccountModel
{
    private string $name;
    private string $email;
    private string $password;

    public function __construct(string $name, string $email, string $password)
    {
        $this->setName($name);
        $this->setEmail($email);
        $this->setPassword($password);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
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
