<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Mocks\MockReturnSamples;
use Tests\TestCase;

class SignInRouteIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    private function mockAccount(bool $validEmail = true, bool $validPassword = true): array
    {
        return [
            "email" => ($validEmail ? "valid_email@email.com" : "invalid_email"),
            "password" => ($validPassword ? "password" : "invalid_password")
        ];
    }

    public function testShouldReturn400IfNoDataWasProvided()
    {
        $response = $this->post('/api/account/signin');

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testShouldReturn400IfInvalidEmailWasProvided()
    {
        $response = $this->post('/api/account/signin', $this->mockAccount(validEmail: false));

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(MockReturnSamples::mockInvalidEmailReturn(), $response->getContent());
    }

    public function testShouldReturn400IfUnauthenticated()
    {
        $response = $this->post('/api/account/signin', $this->mockAccount());

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(MockReturnSamples::mockUnauthenticatedReturn(), $response->getContent());
    }
}
