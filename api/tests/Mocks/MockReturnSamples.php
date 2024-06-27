<?php

namespace Tests\Mocks;

abstract class MockReturnSamples
{
    static function mockInvalidEmailReturn(): string
    {
        return '{"error":"Invalid param: email"}';
    }

    static function mockInvalidPasswordReturn(): string
    {
        return '{"error":"Invalid param: passwordConfirmation"}';
    }
}
