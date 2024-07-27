<?php

namespace Tests\Unit\Data;

use App\Data\Protocols\AddPasswordRepository;
use App\Data\Protocols\Encrypter;
use App\Data\UseCases\DbAddPassword;
use App\Domain\Models\AddPasswordModel;
use App\Domain\Models\PasswordModel;
use Error;
use Tests\TestCase;

class DbAddPasswordTest extends TestCase
{
    private DbAddPassword $sut;

    private Encrypter $encrypterStub;
    private AddPasswordRepository $addPasswordRepositoryStub;

    public function setUp(): void
    {
        $this->encrypterStub = $this->createMock(Encrypter::class);
        $this->addPasswordRepositoryStub = $this->createMock(AddPasswordRepository::class);

        $this->sut = new DbAddPassword(
            $this->encrypterStub,
            $this->addPasswordRepositoryStub
        );
    }

    private function mockAddPasswordModel()
    {
        return new AddPasswordModel('valid_password_account', 'valid_password', 'valid_domain', 1);
    }

    private function mockEncryptedPassword()
    {
        return 'hashed_password';
    }

    private function mockPasswordModel(AddPasswordModel $addPasswordModel)
    {
        $passwordModel = new PasswordModel();
        $passwordModel->setPasswordAccount($addPasswordModel->getPasswordAccount());
        $passwordModel->setPassword($this->mockEncryptedPassword());
        $passwordModel->setDomain($addPasswordModel->getDomain());
        $passwordModel->setAccountId($addPasswordModel->getAccountId());

        return $passwordModel;
    }

    private function mockId()
    {
        return 1;
    }

    public function testEnsureCorrectInstance()
    {
        $this->assertInstanceOf(DbAddPassword::class, $this->sut);
    }

    public function testShouldThrowIfEncrypterThrows()
    {
        $this->encrypterStub->method('encrypt')
            ->willThrowException(new Error());

        $this->expectException(Error::class);

        $this->sut->add($this->mockAddPasswordModel());
    }

    public function testShouldEncrypterHaveBeenCalledWithCorrectPassword()
    {
        $passwordModel = $this->mockAddPasswordModel();

        $this->encrypterStub->expects($this->once())
            ->method('encrypt')
            ->with($passwordModel->getPassword());

        $this->sut->add($passwordModel);
    }

    public function testShouldThrowIfAddPasswordRepositoryThrows()
    {
        $passwordModel = $this->mockAddPasswordModel();

        $this->encrypterStub->method('encrypt')
            ->willReturn($this->mockEncryptedPassword());

        $this->addPasswordRepositoryStub->method('add')
            ->willThrowException(new Error());

        $this->expectException(Error::class);

        $this->sut->add($passwordModel);
    }

    public function testShouldAddPasswordRepositoryHaveBeenCalledWithCorrectPasswordModel()
    {
        $passwordModel = $this->mockAddPasswordModel();

        $this->encrypterStub->method('encrypt')
            ->willReturn($this->mockEncryptedPassword());

        $this->addPasswordRepositoryStub->expects($this->once())
            ->method('add')
            ->with($this->mockPasswordModel($passwordModel));

        $this->sut->add($passwordModel);
    }

    public function testShouldAnPasswordModelOnSuccess()
    {
        $addPasswordModel = $this->mockAddPasswordModel();

        $this->encrypterStub->method('encrypt')
            ->willReturn($this->mockEncryptedPassword());

        $passwordModel = $this->mockPasswordModel($addPasswordModel);

        $this->addPasswordRepositoryStub->method('add')
            ->willReturn($this->mockId());

        $result = $this->sut->add($addPasswordModel);

        $this->assertEquals($result->getId(), $this->mockId());
        $this->assertEquals($passwordModel->getAccountId(), $result->getId());
        $this->assertEquals($passwordModel->getPasswordAccount(), $result->getPasswordAccount());
        $this->assertEquals($passwordModel->getPassword(), $result->getPassword());
        $this->assertEquals($passwordModel->getDomain(), $result->getDomain());
    }
}
