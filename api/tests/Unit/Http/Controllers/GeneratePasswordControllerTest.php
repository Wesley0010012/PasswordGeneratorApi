<?php

namespace Tests\Unit\Http\Controllers;

use App\Domain\Models\AccountModel;
use App\Domain\Models\FindAccountModel;
use App\Domain\UseCases\FindAccount;
use App\Domain\UseCases\GeneratePassword;
use App\Exceptions\InternalServerError;
use App\Exceptions\InvalidParamError;
use App\Exceptions\MissingParamError;
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
    private GeneratePassword $generatePasswordStub;

    public function setUp(): void
    {
        $this->tokenDecrypterStub = $this->createMock(TokenDecrypter::class);
        $this->findAccountStub = $this->createMock(FindAccount::class);
        $this->generatePasswordStub = $this->createMock(GeneratePassword::class);

        $this->sut = new GeneratePasswordController(
            $this->tokenDecrypterStub,
            $this->findAccountStub,
            $this->generatePasswordStub
        );
    }

    private function mockTokenAccount()
    {
        return [
            'email' => 'any_email',
            'password' => 'any_password'
        ];
    }

    private function mockAccount(array $tokenAccount)
    {
        return new FindAccountModel($tokenAccount['email'], $tokenAccount['password']);
    }

    private function mockAccountModel()
    {
        return new AccountModel();
    }

    private function mockGeneratedPassword()
    {
        return 'valid_password_output';
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

        $error = new InternalServerError();

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

    public function testShouldReturn500IfFindAccountThrows()
    {
        $this->tokenDecrypterStub->method('decrypt')
            ->willReturn($this->mockTokenAccount());

        $this->findAccountStub->method('getAccount')
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

    public function testShouldFindAccountHaveBeenCalledWithCorrectFindAccountModel()
    {
        $this->tokenDecrypterStub->method('decrypt')
            ->willReturn($this->mockTokenAccount());

        $this->findAccountStub->expects($this->once())
            ->method('getAccount')
            ->with($this->mockAccount($this->mockTokenAccount()));

        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'token' => 'any_token',
            'size' => 1
        ]);

        $this->sut->handle($httpRequest);
    }

    public function testShouldReturn500IfGeneratePasswordThrows()
    {
        $this->tokenDecrypterStub->method('decrypt')
            ->willReturn($this->mockTokenAccount());

        $this->findAccountStub->method('getAccount')
            ->willReturn($this->mockAccountModel());

        $this->generatePasswordStub->method('generate')
            ->willThrowException(new Error());

        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'token' => 'any_token',
            'size' => 1
        ]);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new InternalServerError();

        $this->assertEquals(500, $httpResponse->getStatusCode());
        $this->assertInstanceOf(InternalServerError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }

    public function testShouldGeneratePasswordHaveBeenCalledWithCorrectPasswordSize()
    {
        $this->tokenDecrypterStub->method('decrypt')
            ->willReturn($this->mockTokenAccount());

        $this->findAccountStub->method('getAccount')
            ->willReturn($this->mockAccountModel());

        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'token' => 'any_token',
            'size' => 1
        ]);

        $this->generatePasswordStub->expects($this->once())
            ->method('generate')
            ->with($httpRequest->getBody()['size']);

        $this->sut->handle($httpRequest);
    }

    public function testShouldReturn200OnSuccess()
    {
        $this->tokenDecrypterStub->method('decrypt')
            ->willReturn($this->mockTokenAccount());

        $this->findAccountStub->method('getAccount')
            ->willReturn($this->mockAccountModel());

        $this->generatePasswordStub->method('generate')
            ->willReturn($this->mockGeneratedPassword());

        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'token' => 'any_token',
            'size' => 1
        ]);

        $this->generatePasswordStub->expects($this->once())
            ->method('generate')
            ->with($httpRequest->getBody()['size']);

        $httpResponse = $this->sut->handle($httpRequest);

        $this->assertEquals(200, $httpResponse->getStatusCode());
        $this->assertEquals(['password' => $this->mockGeneratedPassword()], $httpResponse->getBody());
    }
}
