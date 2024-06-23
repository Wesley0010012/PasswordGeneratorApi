<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class AccountModel extends Model
{
    private int $id;
    private string $name;
    private string $email;
    private string $password;

    protected $table = 'accounts';

    protected $primaryKey = 'acc_id';
    public $timestamps = false;

    protected $fillable = [
        "acc_name",
        "acc_email",
        "acc_password"
    ];

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
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
