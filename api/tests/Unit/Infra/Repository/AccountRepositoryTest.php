<?php

namespace Tests\Unit\Infra\Repository;

use App\Domain\Models\AccountModel;
use App\Infra\Repository\AccountRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private AccountRepository $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new AccountRepository();
    }

    private function mockAccountModel()
    {
        $accountModel = new AccountModel();
        $accountModel->setName("valid_name");
        $accountModel->setEmail("valid_email@email.com");
        $accountModel->setPassword("valid_password");

        return $accountModel;
    }

    private function populateDatabase(AccountModel $accountModel): void
    {
        (new AccountModel([
            "acc_name" => $accountModel->getName(),
            "acc_email" => $accountModel->getEmail(),
            "acc_password" => $accountModel->getPassword(),
        ]))->save();
    }

    public function testEnsureCorrectInstance()
    {
        $this->assertInstanceOf(AccountRepository::class, $this->sut);
    }

    public function testShouldSaveAnAccountOnSuccess()
    {
        $accountModel = $this->mockAccountModel();

        $id = $this->sut->add($accountModel);

        $this->assertTrue(!!$id);

        $this->assertDatabaseHas('accounts', [
            "acc_name" => $accountModel->getName(),
            "acc_email" => $accountModel->getEmail(),
            "acc_password" => $accountModel->getPassword()
        ]);
    }

    public function testShouldReturnAnAccountEmailIfExists()
    {
        $accountModel = $this->mockAccountModel();

        $this->populateDatabase($accountModel);

        $result = $this->sut->findAccountByEmail($accountModel->getEmail())?->getAttributes();

        $this->assertNotNull($result);
        $this->assertEquals($accountModel->getName(), $result['acc_name']);
        $this->assertEquals($accountModel->getEmail(), $result['acc_email']);
        $this->assertEquals($accountModel->getPassword(), $result['acc_password']);
    }

    public function testShouldReturnNullIfEmailNotExists()
    {
        $accountModel = $this->mockAccountModel();

        $result = $this->sut->findAccountByEmail($accountModel->getEmail())?->getAttributes();

        $this->assertNull($result);
    }
}
