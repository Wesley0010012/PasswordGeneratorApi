<?php

namespace Tests\Unit\Data;

use App\Data\Protocols\CheckPasswordRepository;
use App\Data\UseCases\DbCheckPassword;
use App\Domain\Models\FindPasswordModel;
use App\Domain\Models\PasswordModel;
use Error;
use Tests\TestCase;

class DbCheckPasswordTest extends TestCase
{
    private DbCheckPassword $sut;

    private CheckPasswordRepository $checkPasswordRepositoryStub;

    public function setUp(): void
    {
        $this->checkPasswordRepositoryStub = $this->createMock(CheckPasswordRepository::class);

        $this->sut = new DbCheckPassword(
            $this->checkPasswordRepositoryStub
        );
    }

    private function mockFindPasswordModel()
    {
        return new FindPasswordModel(
            1,
            'any_account',
            'any_domain'
        );
    }

    public function testEnsureCorrectInstance()
    {
        $this->assertInstanceOf(DbCheckPassword::class, $this->sut);
    }

    public function testShouldThrowIfCheckPasswordRepositoryThrows()
    {
        $this->checkPasswordRepositoryStub->method('findPasswordByModel')
            ->willThrowException(new Error());

        $this->expectException(Error::class);

        $this->sut->check($this->mockFindPasswordModel());
    }

    public function testShouldCheckPasswordRepositoryHaveBeenCalledWithCorrectFindPasswordModel()
    {
        $this->checkPasswordRepositoryStub->expects($this->once())
            ->method('findPasswordByModel')
            ->with($this->mockFindPasswordModel());

        $this->sut->check($this->mockFindPasswordModel());
    }

    public function testShouldReturnTrueIfCheckPasswordRepositoryReturnsAnPasswordModel()
    {
        $this->checkPasswordRepositoryStub
            ->method('findPasswordByModel')
            ->willReturn(new PasswordModel());

        $result = $this->sut->check($this->mockFindPasswordModel());

        $this->assertTrue($result);
    }
}
