<?php

namespace Tests\Unit\Http\Controllers;

use App\Exceptions\MissingParamError;
use App\Http\Controllers\SignInController;
use App\Http\Protocols\HttpRequest;
use Tests\TestCase;

class SignInControllerTest extends TestCase
{
    private SignInController $sut;

    public function setUp(): void
    {
        $this->sut = new SignInController();
    }

    public function testEnsureCorrectInstance()
    {
        $this->assertInstanceOf(SignInController::class, $this->sut);
    }

    public function testShouldReturn400IfNoEmailWasProvided()
    {
        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'email' => '',
            'password' => 'any_password'
        ]);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new MissingParamError('email');

        $this->assertEquals(400, $httpResponse->getStatusCode());
        $this->assertInstanceOf(MissingParamError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }

    public function testShouldReturn400IfNoPasswordWasProvided()
    {
        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'email' => 'any_email',
            'password' => ''
        ]);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new MissingParamError('password');

        $this->assertEquals(400, $httpResponse->getStatusCode());
        $this->assertInstanceOf(MissingParamError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }
}
