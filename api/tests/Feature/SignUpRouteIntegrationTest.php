<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
