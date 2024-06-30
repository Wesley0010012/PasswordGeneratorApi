<?php

namespace Tests\Unit\Http\Controllers;

use App\Exceptions\InternalServerError;
use App\Exceptions\InvalidParamError;
use App\Exceptions\MissingParamError;
use App\Http\Controllers\SignInController;
use App\Http\Protocols\EmailValidator;
use App\Http\Protocols\HttpRequest;
use Error;
use Tests\TestCase;

class SignInControllerTest extends TestCase
{
    private SignInController $sut;
    private EmailValidator $emailValidatorStub;

    public function setUp(): void
    {
        $this->emailValidatorStub = $this->createMock(EmailValidator::class);

        $this->sut = new SignInController(
            $this->emailValidatorStub
        );
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

    public function testShouldReturn400IfInvalidEmailWasProvided()
    {
        $this->emailValidatorStub->method('validate')
            ->willReturn(false);

        $httpRequest = new HttpRequest();

        $body = [
            'email' => 'invalid_email',
            'password' => 'any_password'
        ];

        $httpRequest->setBody($body);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new InvalidParamError('email');

        $this->assertEquals(400, $httpResponse->getStatusCode());
        $this->assertInstanceOf(InvalidParamError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }

    public function testShouldEmailValidatorHaveBeenCalledWithCorrectEmail()
    {
        $httpRequest = new HttpRequest();

        $body = [
            'name' => 'any_name',
            'email' => 'any_email',
            'password' => 'any_password',
            'passwordConfirmation' => 'any_password'
        ];

        $httpRequest->setBody($body);

        $this->emailValidatorStub->expects($this->once())
            ->method('validate')
            ->with($body['email']);

        $this->sut->handle($httpRequest);
    }

    public function testShouldReturn500IfEmailValidatorThrows()
    {
        $this->emailValidatorStub->method('validate')
            ->willThrowException(new Error());

        $httpRequest = new HttpRequest();

        $body = [
            'name' => 'any_name',
            'email' => 'any_email',
            'password' => 'any_password',
            'passwordConfirmation' => 'any_password'
        ];

        $httpRequest->setBody($body);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new InternalServerError();

        $this->assertEquals(500, $httpResponse->getStatusCode());
        $this->assertInstanceOf(InternalServerError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }
}
