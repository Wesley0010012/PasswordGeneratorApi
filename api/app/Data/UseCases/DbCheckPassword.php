<?php

namespace App\Data\UseCases;

use App\Data\Protocols\CheckPasswordRepository;
use App\Domain\Models\FindPasswordModel;
use App\Domain\UseCases\CheckPassword;

class DbCheckPassword implements CheckPassword
{
    public function __construct(
        private readonly CheckPasswordRepository $checkPasswordRepository
    ) {
    }

    public function check(FindPasswordModel $findPasswordModel): bool
    {
        return !!$this->checkPasswordRepository->findPasswordByModel($findPasswordModel);
    }
}
