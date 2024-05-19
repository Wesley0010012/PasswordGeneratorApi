<?php

namespace Tests\Unit\Adapters;

use App\Adapters\EmailValidatorAdapter;
use Tests\TestCase;

class EmailValidatorAdapterTest extends TestCase
{
    private EmailValidatorAdapter $sut;

    public function setUp(): void
    {
        $this->sut = new EmailValidatorAdapter();
    }

    public function testEnsureCorrectInstance()
    {
        $this->assertInstanceOf(EmailValidatorAdapter::class, $this->sut);
    }

    public function testEnsureReturnsFalseIfInvalidEmail()
    {
        $result = $this->sut->validate("invalid_email");

        $this->assertFalse($result);
    }
}
