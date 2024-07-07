<?php

namespace Tests\Unit\Adapters;

use App\Adapters\TokenDecrypterAdapter;
use Tests\TestCase;

class TokenDecrypterAdapterTest extends TestCase
{
    private TokenDecrypterAdapter $sut;

    public function setUp(): void
    {
        $this->sut = new TokenDecrypterAdapter();
    }

    public function testEnsureCorrectInstance()
    {
        $this->assertInstanceOf(TokenDecrypterAdapter::class, $this->sut);
    }

    public function testShouldReturnFalseIfInvalidTokenWasProvided()
    {
        $this->assertFalse($this->sut->decrypt('invalid_token'));
    }

    public function testShouldReturnAnArrayOnSuccess()
    {
        $result = $this->sut->decrypt(base64_encode('valid_email,valid_password'));

        $this->assertNotFalse($result);
        $this->assertEquals('valid_email', $result['email']);
        $this->assertEquals('valid_password', $result['password']);
    }
}