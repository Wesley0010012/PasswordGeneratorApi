<?php

namespace Tests\Feature;

use App\Adapters\TokenGeneratorAdapter;
use App\Domain\Models\AccountModel;
use App\Infra\Encrypters\AES256Adapter;
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
        $this->password = (new AES256Adapter())->encrypt("password");
    }

    private function mockRequest(bool $validToken = true, bool $validSize = true): array
    {
        return [
            "token" => ($validToken ? $this->mockToken()['token'] : 'invalid_token'),
            "size" => ($validSize ? 32 : -1)
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
        $accountModel->setEmail("valid_email@email.com");
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

    public function testShouldReturn400IfInvalidSizeWasProvided()
    {
        $response = $this->post('/api/password/generate-password', $this->mockRequest(validSize: false));

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(MockReturnSamples::mockInvalidSizeReturn(), $response->getContent());
    }

    public function testShouldReturn200OnSuccess()
    {
        $response = $this->post('/api/password/generate-password', $this->mockRequest());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotNull(($response->getContent()));
    }
}
