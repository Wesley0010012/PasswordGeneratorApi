<?php

namespace App\Infra\Repository;

use App\Data\Protocols\AddAccountRepository;
use App\Data\Protocols\CheckAccountRepository;
use App\Data\Protocols\FindAccountRepository;
use App\Domain\Models\AccountModel;
use App\Domain\Models\FindAccountModel;

class AccountRepository implements
    AddAccountRepository,
    CheckAccountRepository,
    FindAccountRepository
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

    public function findAccountData(FindAccountModel $findAccountModel): ?AccountModel
    {
        $accountModel = AccountModel::where('acc_email', '=', $findAccountModel->getEmail())
            ->where('acc_password', '=', $findAccountModel->getPassword())
            ->first();

        if (!$accountModel) {
            return null;
        }

        $data = $accountModel?->getAttributes();

        $result = new AccountModel();

        $result->setId($data['acc_id']);
        $result->setName($data['acc_name']);
        $result->setEmail($data['acc_email']);
        $result->setPassword($data['acc_password']);

        return $result;
    }
}
