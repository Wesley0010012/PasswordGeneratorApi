<?php

namespace Tests\Unit\Infra\Encrypters;

use App\Infra\Encrypters\AES256Adapter;
use Tests\TestCase;

class AES256AdapterTest extends TestCase
{
    private AES256Adapter $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new AES256Adapter();
    }

    private function mockEncryptedString()
    {
        return 'UGcYWM9mou1Mych0oPNPEQ==';
    }

    public function testShouldReturnTheCorrectEncryptedPasswordOnSuccess()
    {
        $plaintext = "ABCD1234";

        $result = $this->sut->encrypt($plaintext);

        $this->assertEquals($this->mockEncryptedString(), $result);
    }
}
