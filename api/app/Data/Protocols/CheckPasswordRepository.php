<?php

namespace App\Data\Protocols;

use App\Domain\Models\FindPasswordModel;
use App\Domain\Models\PasswordModel;

interface CheckPasswordRepository
{
    public function findPasswordByModel(FindPasswordModel $findPasswordModel): ?PasswordModel;
}
