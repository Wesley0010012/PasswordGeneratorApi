<?php

namespace Tests\Unit\Adapters;

use App\Adapters\TokenGeneratorAdapter;
use App\Domain\Models\AccountModel;
use Tests\TestCase;

class TokenGeneratorAdapterTest extends TestCase
{
    private TokenGeneratorAdapter $sut;

    public function setUp(): void
    {
        $this->sut = new TokenGeneratorAdapter();
    }

    private function mockAccountModel(): AccountModel
    {
        $accountModel = new AccountModel();
        $accountModel->setName("valid_account");
        $accountModel->setEmail("valid_email");
        $accountModel->setPassword("valid_password");

        return $accountModel;
    }

    private function mockResult(AccountModel $accountModel)
    {
        return json_encode([
            "account" => $accountModel->getName(),
            "email" => $accountModel->getEmail(),
            "password" => $accountModel->getPassword()
        ]);
    }

    public function testEnsureCorrectInstance()
    {
        $this->assertInstanceOf(TokenGeneratorAdapter::class, $this->sut);
    }

    public function testShouldReturnAnJsonTokenAccountOnSuccess()
    {
        $accountModel = $this->mockAccountModel();

        $result = $this->sut->generate($accountModel);

        $this->assertEquals($this->mockResult($accountModel), $result);
    }
}
