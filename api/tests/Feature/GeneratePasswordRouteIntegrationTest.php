<?php

namespace Tests\Feature;

use App\Adapters\TokenGeneratorAdapter;
use App\Domain\Models\AccountModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Mocks\MockReturnSamples;
use Tests\TestCase;

class GeneratePasswordRouteIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private string $password;

    public function setUp(): void
    {
        parent::setUp();
        $this->password = "password";
    }

    private function mockRequest(bool $validToken = true, bool $validSize = true): array
    {
        return [
            "token" => ($validToken ? $this->mockToken() : 'invalid_token'),
            "size" => ($validSize ? 32 : 0)
        ];
    }

    private function populateDatabase(): AccountModel
    {
        (new AccountModel([
            "acc_name" => "valid_name",
            "acc_email" => "valid_email@email.com",
            "acc_password" => $this->password
        ]))->save();

        $accountModel = new AccountModel();
        $accountModel->setEmail("valid_email");
        $accountModel->setPassword($this->password);

        return $accountModel;
    }

    private function mockToken()
    {
        return (new TokenGeneratorAdapter())->generate($this->populateDatabase());
    }

    public function testShouldReturn400IfNoDataWasProvided()
    {
        $response = $this->post('/api/password/generate-password');

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testShouldReturn400IfInvalidTokenWasProvided()
    {
        $response = $this->post('/api/password/generate-password', $this->mockRequest(validToken: false));

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(MockReturnSamples::mockInvalidTokenReturn(), $response->getContent());
    }
}
