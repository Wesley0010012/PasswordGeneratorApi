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

    static function mockSuccessReturn(): string
    {
        return '{"token":"dmFsaWRfZW1haWxAZW1haWwuY29tLExUNFdrY1NESUdTNXFYNUExaENqYlE9PQ=="}';
    }

    static function mockUnauthenticatedReturn(): string
    {
        return '{"error":"Unauthenticated account with email: valid_email@email.com"}';
    }
}
