<?php

namespace Tests\Unit\Data\UseCases;

use App\Data\Protocols\Encrypter;
use App\Data\Protocols\FindAccountRepository;
use App\Data\UseCases\DbFindAccount;
use App\Domain\Models\FindAccountModel;
use Error;
use Tests\TestCase;

class DbFindAccountTest extends TestCase
{
    private DbFindAccount $sut;

    private Encrypter $encrypterStub;
    private FindAccountRepository $findAccountRepositoryStub;

    public function setUp(): void
    {
        $this->encrypterStub = $this->createMock(Encrypter::class);
        $this->findAccountRepositoryStub = $this->createMock(FindAccountRepository::class);

        $this->sut = new DbFindAccount(
            $this->encrypterStub,
            $this->findAccountRepositoryStub
        );
    }

    private function mockFindAccountModel()
    {
        return new FindAccountModel(
            'valid_email',
            'valid_password'
        );
    }

    private function mockCypheredPassword()
    {
        return 'cyphered_password';
    }

    public function testEnsureCorrectInstance()
    {
        $this->assertInstanceOf(DbFindAccount::class, $this->sut);
    }

    public function testShouldThrowIfEncrypterThrows()
    {
        $this->expectException(Error::class);

        $this->encrypterStub->method('encrypt')
            ->willThrowException(new Error());

        $data = $this->mockFindAccountModel();

        $this->sut->getAccount($data);
    }

    public function testShouldEncrypterHaveBeenCalledWithCorrectPassword()
    {
        $data = $this->mockFindAccountModel();

        $this->encrypterStub->expects($this->once())
            ->method('encrypt')
            ->with($data->getPassword());

        $this->sut->getAccount($data);
    }

    public function testShouldThrowIfFindAccountRepositoryThrows()
    {
        $this->expectException(Error::class);

        $this->encrypterStub->method('encrypt')
            ->willReturn($this->mockCypheredPassword());

        $this->findAccountRepositoryStub->method('findAccountData')
            ->willThrowException(new Error());

        $data = $this->mockFindAccountModel();

        $this->sut->getAccount($data);
    }
}
