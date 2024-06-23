<?php

namespace Tests\Unit\Data;

use App\Data\Protocols\AddAccountRepository;
use App\Data\Protocols\Encrypter;
use App\Data\UseCases\DbAddAccount;
use App\Domain\Models\AccountModel;
use App\Domain\Models\AddAccountModel;
use Error;
use Tests\TestCase;

class DbAddAccountTest extends TestCase
{
    private DbAddAccount $sut;

    private AddAccountRepository $addAccountRepositoryStub;
    private Encrypter $encrypterStub;

    public function setUp(): void
    {
        $this->addAccountRepositoryStub = $this->createMock(AddAccountRepository::class);
        $this->encrypterStub = $this->createMock(Encrypter::class);

        $this->sut = new DbAddAccount(
            $this->addAccountRepositoryStub,
            $this->encrypterStub
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

    private function mockCypheredPassword()
    {
        return 'cyphered_password';
    }

    private function mockAccountModel(AddAccountModel $addAccountModel)
    {
        $accountModel = new AccountModel();
        $accountModel->setName($addAccountModel->getName());
        $accountModel->setEmail($addAccountModel->getEmail());
        $accountModel->setPassword($this->mockCypheredPassword());

        return $accountModel;
    }

    public function testEnsureCorrectInstance()
    {
        $this->assertInstanceOf(DbAddAccount::class, $this->sut);
    }

    public function testShouldThrowIfEncrypterThrows()
    {
        $this->expectException(Error::class);

        $this->encrypterStub->method('encrypt')
            ->willThrowException(new Error());

        $data = $this->mockAddAccountModel();

        $this->sut->add($data);
    }

    public function testShouldEncrypterHaveBeenCalledWithCorrectPassword()
    {
        $data = $this->mockAddAccountModel();

        $this->encrypterStub->expects($this->once())
            ->method('encrypt')
            ->with($data->getPassword());

        $this->sut->add($data);
    }

    public function testShouldThrowIfAddAccountThrows()
    {
        $this->expectException(Error::class);

        $data = $this->mockAddAccountModel();

        $this->addAccountRepositoryStub->method('add')
            ->willThrowException(new Error());


        $this->sut->add($data);
    }

    public function testShouldAddAccountRepositoryHaveBeenCalledWithCorrectAddAccountModel()
    {
        $data = $this->mockAddAccountModel();

        $accountModel = $this->mockAccountModel($data);

        $this->encrypterStub->method('encrypt')
            ->willReturn($this->mockCypheredPassword());

        $this->addAccountRepositoryStub->expects($this->once())
            ->method('add')
            ->with($accountModel);

        $this->sut->add($data);
    }

    public function testShouldReturnAnAccountModelOnSuccess()
    {
        $data = $this->mockAddAccountModel();

        $this->encrypterStub->method('encrypt')
            ->willReturn($this->mockCypheredPassword());

        $this->addAccountRepositoryStub->method('add')
            ->willReturn(9999);


        $result = $this->sut->add($data);

        $this->assertIsNumeric(9999, $result->getId());
        $this->assertEquals($data->getName(), $result->getName());
        $this->assertEquals($data->getEmail(), $result->getEmail());
        $this->assertEquals($this->mockCypheredPassword(), $result->getPassword());
    }
}