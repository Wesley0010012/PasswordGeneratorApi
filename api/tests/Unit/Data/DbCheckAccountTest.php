<?php

namespace Tests\Unit\Data;

use App\Data\Protocols\CheckAccountRepository;
use App\Data\UseCases\DbCheckAccount;
use Tests\TestCase;

class DbCheckAccountTest extends TestCase
{
    private DbCheckAccount $sut;

    private CheckAccountRepository $checkAccountRepositoryStub;

    public function setUp(): void
    {
        $this->checkAccountRepositoryStub = $this->createMock(CheckAccountRepository::class);

        $this->sut = new DbCheckAccount(
            $this->checkAccountRepositoryStub
        );
    }

    public function testEnsureCorrectInstance()
    {
        $this->assertInstanceOf(DbCheckAccount::class, $this->sut);
    }

    public function testShouldReturnFalseIfDbCheckAccountRepositoryReturnsNull()
    {
        $email = 'any_email@email.com';

        $this->checkAccountRepositoryStub->method('findAccountByEmail')
            ->willReturn(null);

        $result = $this->sut->verifyIfExists($email);

        $this->assertFalse($result);
    }
}
