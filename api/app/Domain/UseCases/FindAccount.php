<?php

namespace App\Domain\UseCases;

use App\Domain\Models\AccountModel;

interface FindAccount
{
    public function getAccount(): ?AccountModel;
}
