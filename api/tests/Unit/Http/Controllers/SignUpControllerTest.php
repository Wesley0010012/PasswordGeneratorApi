<?php

namespace Tests\Unit\Http\Controllers;

use App\Exceptions\MissingParamError;
use App\Http\Controllers\SignUpController;
use App\Http\Protocols\HttpRequest;
use Tests\TestCase;

class SignUpControllerTest extends TestCase
{
    private SignUpController $sut;

    public function setUp(): void
    {
        $this->sut = new SignUpController();
    }

    public function testEnsureCorrectInstance()
    {
        $this->assertInstanceOf(SignUpController::class, $this->sut);
    }

    public function testShouldReturn400IfNoNameWasProvided()
    {
        $httpRequest = new HttpRequest();

        $body = [
            'name' => '',
            'email' => 'any_email',
            'password' => 'any_password',
            'passwordConfirmation' => 'any_password'
        ];

        $httpRequest->setBody($body);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new MissingParamError('name');

        $this->assertEquals(400, $httpResponse->getStatusCode());
        $this->assertInstanceOf(MissingParamError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }

    public function testShouldReturn400IfNoEmailWasProvided()
    {
        $httpRequest = new HttpRequest();

        $body = [
            'name' => 'any_name',
            'email' => '',
            'password' => 'any_password',
            'passwordConfirmation' => 'any_password'
        ];

        $httpRequest->setBody($body);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new MissingParamError('email');

        $this->assertEquals(400, $httpResponse->getStatusCode());
        $this->assertInstanceOf(MissingParamError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }

    public function testShouldReturn400IfNoPasswordWasProvided()
    {
        $httpRequest = new HttpRequest();

        $body = [
            'name' => 'any_name',
            'email' => 'any_email',
            'password' => '',
            'passwordConfirmation' => 'any_password'
        ];

        $httpRequest->setBody($body);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new MissingParamError('password');

        $this->assertEquals(400, $httpResponse->getStatusCode());
        $this->assertInstanceOf(MissingParamError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }
}
