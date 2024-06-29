<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Mocks\MockReturnSamples;
use Tests\TestCase;

class SignUpRouteIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    private function mockAccount(bool $validEmail = true, bool $validPassword = true): array
    {
        return [
            "name" => "name",
            "email" => ($validEmail ? "valid_email@email.com" : "invalid_email"),
            "password" => "password",
            "passwordConfirmation" => ($validPassword ? "password" : "invalid_password")
        ];
    }

    public function testShouldReturn400IfNoDataWasProvided()
    {
        $response = $this->post('/api/account/signup');

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testShouldReturn400IfInvalidEmailWasProvided()
    {
        $response = $this->post('/api/account/signup', $this->mockAccount(validEmail: false));

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(MockReturnSamples::mockInvalidEmailReturn(), $response->getContent());
    }

    public function testShouldReturn400IfInvalidPasswordConfirmationWasProvided()
    {
        $response = $this->post('/api/account/signup', $this->mockAccount(validPassword: false));

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(MockReturnSamples::mockInvalidPasswordReturn(), $response->getContent());
    }

    public function testShouldReturn200OnSuccess()
    {
        $response = $this->post('/api/account/signup', $this->mockAccount());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(MockReturnSamples::mockSuccessReturn(), $response->getContent());
    }
}
