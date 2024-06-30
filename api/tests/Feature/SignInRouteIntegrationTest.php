<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
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
            "passwordConfirmation" => ($validPassword ? "password" : "invalid_password")
        ];
    }

    public function testShouldReturn400IfNoDataWasProvided()
    {
        $response = $this->post('/api/account/signin');

        $this->assertEquals(400, $response->getStatusCode());
    }
}
