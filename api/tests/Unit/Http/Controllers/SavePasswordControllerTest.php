<?php

namespace Tests\Unit\Http\Controllers;

use App\Exceptions\InternalServerError;
use App\Exceptions\MissingParamError;
use App\Exceptions\UnauthorizedError;
use App\Http\Controllers\SavePasswordController;
use App\Http\Protocols\HttpRequest;
use App\Http\Protocols\TokenDecrypter;
use Error;
use Tests\TestCase;

class SavePasswordControllerTest extends TestCase
{
    private SavePasswordController $sut;
    private TokenDecrypter $tokenDecrypterStub;

    public function setUp(): void
    {
        $this->tokenDecrypterStub = $this->createMock(TokenDecrypter::class);

        $this->sut = new SavePasswordController(
            $this->tokenDecrypterStub
        );
    }

    public function testEnsureCorrectInstance()
    {
        $this->assertInstanceOf(SavePasswordController::class, $this->sut);
    }

    public function testShouldReturn400IfNoTokenWasProvided()
    {
        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'token' => '',
            'email' => 'any_email@email.com',
            'password' => 'any_password',
            'domain' => 'any_domain'
        ]);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new MissingParamError('token');

        $this->assertEquals(400, $httpResponse->getStatusCode());
        $this->assertInstanceOf(MissingParamError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }

    public function testShouldReturn400IfNoEmailWasProvided()
    {
        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'token' => 'any_token',
            'email' => '',
            'password' => 'any_password',
            'domain' => 'any_domain'
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
            'token' => 'any_token',
            'email' => 'any_email@email.com',
            'password' => '',
            'domain' => 'any_domain'
        ]);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new MissingParamError('password');

        $this->assertEquals(400, $httpResponse->getStatusCode());
        $this->assertInstanceOf(MissingParamError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }

    public function testShouldReturn400IfNoDomainWasProvided()
    {
        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'token' => 'any_token',
            'email' => 'any_email@email.com',
            'password' => 'any_password',
            'domain' => ''
        ]);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new MissingParamError('domain');

        $this->assertEquals(400, $httpResponse->getStatusCode());
        $this->assertInstanceOf(MissingParamError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }

    public function testShouldReturn400IfInvalidTokenWasProvided()
    {
        $this->tokenDecrypterStub->method('decrypt')
            ->willReturn(false);

        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'token' => 'any_token',
            'email' => 'any_email@email.com',
            'password' => 'any_password',
            'domain' => 'any_domain'
        ]);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new UnauthorizedError($httpRequest->getBody()['token']);

        $this->assertEquals(400, $httpResponse->getStatusCode());
        $this->assertInstanceOf(UnauthorizedError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }

    public function testShouldReturn500IfTokenDecrypterThrows()
    {
        $this->tokenDecrypterStub->method('decrypt')
            ->willThrowException(new Error());

        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'token' => 'any_token',
            'email' => 'any_email@email.com',
            'password' => 'any_password',
            'domain' => 'any_domain'
        ]);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new InternalServerError();

        $this->assertEquals(500, $httpResponse->getStatusCode());
        $this->assertInstanceOf(InternalServerError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }

    public function testShouldTokenDecrypterHaveBeenCalledWithCorrectToken()
    {
        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'token' => 'any_token',
            'email' => 'any_email@email.com',
            'password' => 'any_password',
            'domain' => 'any_domain'
        ]);

        $this->tokenDecrypterStub->expects($this->once())
            ->method('decrypt')
            ->with($httpRequest->getBody()['token']);

        $this->sut->handle($httpRequest);
    }
}
