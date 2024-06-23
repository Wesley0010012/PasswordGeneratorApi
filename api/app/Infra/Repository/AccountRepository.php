<?php

namespace App\Infra\Repository;

use App\Data\Protocols\AddAccountRepository;
use App\Data\Protocols\CheckAccountRepository;
use App\Domain\Models\AccountModel;

class AccountRepository implements
    AddAccountRepository,
    CheckAccountRepository
{
    public function add(AccountModel $accountModel): int
    {
        $model = new AccountModel([
            "acc_name" => $accountModel->getName(),
            "acc_email" => $accountModel->getEmail(),
            "acc_password" => $accountModel->getPassword()
        ]);

        $model->save();

        return $model->acc_id;
    }

    public function findAccountByEmail(string $email): ?AccountModel
    {
        return AccountModel::where('acc_email', '=', $email)->first();
    }
}
