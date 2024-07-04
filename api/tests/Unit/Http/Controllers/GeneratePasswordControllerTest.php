<?php

namespace Tests\Unit\Http\Controllers;

use App\Domain\UseCases\FindAccount;
use App\Exceptions\InternalServerError;
use App\Exceptions\InvalidParamError;
use App\Exceptions\MissingParamError;
use App\Exceptions\UnauthenticatedError;
use App\Exceptions\UnauthorizedError;
use App\Http\Controllers\GeneratePasswordController;
use App\Http\Protocols\HttpRequest;
use App\Http\Protocols\TokenDecrypter;
use Error;
use Tests\TestCase;

class GeneratePasswordControllerTest extends TestCase
{
    private GeneratePasswordController $sut;
    private TokenDecrypter $tokenDecrypterStub;
    private FindAccount $findAccountStub;

    public function setUp(): void
    {
        $this->tokenDecrypterStub = $this->createMock(TokenDecrypter::class);
        $this->findAccountStub = $this->createMock(FindAccount::class);

        $this->sut = new GeneratePasswordController(
            $this->tokenDecrypterStub,
            $this->findAccountStub
        );
    }

    private function mockTokenAccount()
    {
        return [
            'email' => 'any_email',
            'password' => 'any_password'
        ];
    }

    public function testEnsureCorrectInstance()
    {
        $this->assertInstanceOf(GeneratePasswordController::class, $this->sut);
    }

    public function testShouldReturn400IfNoTokenWasProvided()
    {
        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'token' => '',
            'size' => 100
        ]);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new MissingParamError('token');

        $this->assertEquals(400, $httpResponse->getStatusCode());
        $this->assertInstanceOf(MissingParamError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }

    public function testShouldReturn400IfNoSizeWasProvided()
    {
        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'token' => 'any_token',
            'size' => null
        ]);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new MissingParamError('size');

        $this->assertEquals(400, $httpResponse->getStatusCode());
        $this->assertInstanceOf(MissingParamError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }


    public function testShouldReturn400IfInvalidPasswordSizeWasProvided()
    {
        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'token' => 'any_token',
            'size' => -1
        ]);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new InvalidParamError('size');

        $this->assertEquals(400, $httpResponse->getStatusCode());
        $this->assertInstanceOf(InvalidParamError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }

    public function testShouldReturn400IfInvalidPasswordTokenWasProvided()
    {
        $this->tokenDecrypterStub->method('decrypt')
            ->willReturn(false);

        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'token' => 'any_token',
            'size' => 1
        ]);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new InvalidParamError('token');

        $this->assertEquals(400, $httpResponse->getStatusCode());
        $this->assertInstanceOf(InvalidParamError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }

    public function testShouldTokenDecrypterHaveBeenCalledWithCorrectToken()
    {
        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'token' => 'any_token',
            'size' => 1
        ]);

        $this->tokenDecrypterStub->expects($this->once())
            ->method('decrypt')
            ->with($httpRequest->getBody()['token']);

        $this->sut->handle($httpRequest);
    }

    public function testShouldReturn500IfTokenDecrypterThrows()
    {
        $this->tokenDecrypterStub->method('decrypt')
            ->willThrowException(new Error());

        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'token' => 'any_token',
            'size' => 1
        ]);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new InternalServerError('token');

        $this->assertEquals(500, $httpResponse->getStatusCode());
        $this->assertInstanceOf(InternalServerError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }

    public function testShouldReturn400IfTokenAccountWasNotFinded()
    {
        $this->tokenDecrypterStub->method('decrypt')
            ->willReturn($this->mockTokenAccount());

        $this->findAccountStub->method('getAccount')
            ->willReturn(null);

        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'token' => 'any_token',
            'size' => 1
        ]);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new UnauthorizedError($httpRequest->getBody()['token']);

        $this->assertEquals(400, $httpResponse->getStatusCode());
        $this->assertInstanceOf(UnauthorizedError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }
}
