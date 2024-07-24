<?php

namespace Tests\Unit\Data;

use App\Data\Protocols\AddPasswordRepository;
use App\Data\Protocols\Encrypter;
use App\Data\UseCases\DbAddPassword;
use App\Domain\Models\AddPasswordModel;
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
}
