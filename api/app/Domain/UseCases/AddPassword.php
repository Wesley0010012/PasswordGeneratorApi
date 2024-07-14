<?php

namespace App\Domain\UseCases;

use App\Domain\Models\AddPasswordModel;
use App\Domain\Models\PasswordModel;

interface AddPassword
{
    public function add(AddPasswordModel $addPasswordModel): PasswordModel;
}
