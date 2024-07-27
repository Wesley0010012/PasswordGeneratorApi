<?php

namespace App\Data\Protocols;

use App\Domain\Models\PasswordModel;

interface AddPasswordRepository
{
    public function add(PasswordModel $addPasswordModel): int;
}
