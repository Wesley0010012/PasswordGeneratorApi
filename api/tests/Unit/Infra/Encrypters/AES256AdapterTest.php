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

    private function mockPlaintextString()
    {
        return 'ABCD1234';
    }

    public function testShouldReturnTheCorrectEncryptedPasswordOnSuccess()
    {
        $result = $this->sut->encrypt($this->mockPlaintextString());

        $this->assertEquals($this->mockEncryptedString(), $result);
    }

    public function testShouldReturnTheDecryptedPasswordOnSuccess()
    {
        $result = $this->sut->decrypt($this->mockEncryptedString());

        $this->assertEquals($this->mockPlaintextString(), $result);
    }
}
