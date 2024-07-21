<?php

namespace Tests\Unit\Data;

use App\Data\Protocols\Encrypter;
use App\Data\UseCases\DbAddPassword;
use App\Domain\Models\AddPasswordModel;
use Error;
use Tests\TestCase;

class DbAddPasswordTest extends TestCase
{
    private DbAddPassword $sut;
    private Encrypter $encrypterStub;

    public function setUp(): void
    {
        $this->encrypterStub = $this->createMock(Encrypter::class);

        $this->sut = new DbAddPassword(
            $this->encrypterStub
        );
    }

    private function mockPasswordModel()
    {
        return new AddPasswordModel('valid_password_account', 'valid_password', 'valid_domain', 1);
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

        $this->sut->add($this->mockPasswordModel());
    }
}
