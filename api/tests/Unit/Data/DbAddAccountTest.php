<?php

namespace Tests\Unit\Data;

use App\Data\Protocols\AddAccountRepository;
use App\Data\UseCases\DbAddAccount;
use App\Domain\Models\AddAccountModel;
use Error;
use Tests\TestCase;

class DbAddAccountTest extends TestCase
{
    private DbAddAccount $sut;

    private AddAccountRepository $addAccountRepositoryStub;

    public function setUp(): void
    {
        $this->addAccountRepositoryStub = $this->createMock(AddAccountRepository::class);

        $this->sut = new DbAddAccount(
            $this->addAccountRepositoryStub
        );
    }

    private function mockAddAccountModel()
    {
        return new AddAccountModel(
            'valid_name',
            'valid_email',
            'valid_password'
        );
    }

    public function testEnsureCorrectInstance()
    {
        $this->assertInstanceOf(DbAddAccount::class, $this->sut);
    }

    public function testShouldThrowIfAddAccountThrows()
    {
        $this->expectException(Error::class);

        $data = $this->mockAddAccountModel();

        $this->addAccountRepositoryStub->method('add')
            ->willThrowException(new Error());


        $this->sut->add($data);
    }

    public function testShouldAddAccountRepositoryHaveBeenCalledWithWithCorrectAddAccountModel()
    {
        $data = $this->mockAddAccountModel();

        $this->addAccountRepositoryStub->expects($this->once())
            ->method('add')
            ->with($data);

        $this->sut->add($data);
    }
}
