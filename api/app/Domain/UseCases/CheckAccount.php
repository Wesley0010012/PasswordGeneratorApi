<?php

namespace App\Domain\UseCases;

use App\Domain\Models\AccountModel;
use App\Domain\Models\FindAccountModel;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;

interface CheckAccount
{
    public function verifyIfExists(FindAccountModel $accountModel): bool;
}
