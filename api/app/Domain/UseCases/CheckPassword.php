<?php

namespace App\Domain\UseCases;

use App\Domain\Models\FindPasswordModel;

interface CheckPassword
{
    public function check(FindPasswordModel $findPasswordModel): bool;
}
