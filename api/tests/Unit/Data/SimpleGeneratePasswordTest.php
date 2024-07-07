<?php

namespace Tests\Unit\Data;

use App\Data\UseCases\SimpleGeneratePassword;
use Tests\TestCase;

class SimpleGeneratePasswordTest extends TestCase
{
    private SimpleGeneratePassword $sut;

    public function setUp(): void
    {
        $this->sut = new SimpleGeneratePassword();
    }

    public function testEnsureCorrectInstance()
    {
        $this->assertInstanceOf(SimpleGeneratePassword::class, $this->sut);
    }

    public function testShouldReturnAnPasswordWithCorrectSizeOnSuccess()
    {
        $passwordSize = 32;

        $result = $this->sut->generate($passwordSize);

        $this->assertTrue(!!$result);
        $this->assertTrue(strlen($result) === $passwordSize);
    }
}
