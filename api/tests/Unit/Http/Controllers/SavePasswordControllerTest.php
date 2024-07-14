<?php

namespace Tests\Unit\Http\Controllers;

use App\Domain\Models\AccountModel;
use App\Domain\Models\FindAccountModel;
use App\Domain\Models\FindPasswordModel;
use App\Domain\UseCases\AddPassword;
use App\Domain\UseCases\CheckPassword;
use App\Domain\UseCases\FindAccount;
use App\Exceptions\InternalServerError;
use App\Exceptions\InvalidParamError;
use App\Exceptions\MissingParamError;
use App\Exceptions\PasswordAccountExistsError;
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
    private FindAccount $findAccountStub;
    private CheckPassword $checkPasswordStub;
    private AddPassword $addPasswordStub;

    public function setUp(): void
    {
        $this->tokenDecrypterStub = $this->createMock(TokenDecrypter::class);
        $this->findAccountStub = $this->createMock(FindAccount::class);
        $this->checkPasswordStub = $this->createMock(CheckPassword::class);
        $this->addPasswordStub = $this->createMock(AddPassword::class);

        $this->sut = new SavePasswordController(
            $this->tokenDecrypterStub,
            $this->findAccountStub,
            $this->checkPasswordStub,
            $this->addPasswordStub
        );
    }

    private function mockAccount(): array
    {
        return [
            'email' => 'any_email@email.com',
            'password' => 'any_password'
        ];
    }

    private function mockAccountModel(): AccountModel
    {
        $accountModel = new AccountModel();
        $accountModel->setId(1);

        return $accountModel;
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
            'account' => 'any_email@email.com',
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
            'account' => '',
            'password' => 'any_password',
            'domain' => 'any_domain'
        ]);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new MissingParamError('account');

        $this->assertEquals(400, $httpResponse->getStatusCode());
        $this->assertInstanceOf(MissingParamError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }

    public function testShouldReturn400IfNoPasswordWasProvided()
    {
        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'token' => 'any_token',
            'account' => 'any_email@email.com',
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
            'account' => 'any_email@email.com',
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
            'account' => 'any_email@email.com',
            'password' => 'any_password',
            'domain' => 'any_domain'
        ]);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new InvalidParamError($httpRequest->getBody()['token']);

        $this->assertEquals(400, $httpResponse->getStatusCode());
        $this->assertInstanceOf(InvalidParamError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }

    public function testShouldReturn500IfTokenDecrypterThrows()
    {
        $this->tokenDecrypterStub->method('decrypt')
            ->willThrowException(new Error());

        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'token' => 'any_token',
            'account' => 'any_email@email.com',
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
            'account' => 'any_email@email.com',
            'password' => 'any_password',
            'domain' => 'any_domain'
        ]);

        $this->tokenDecrypterStub->expects($this->once())
            ->method('decrypt')
            ->with($httpRequest->getBody()['token']);

        $this->sut->handle($httpRequest);
    }

    public function testShouldReturn400IfTokenAccountWasNotFinded()
    {
        $this->tokenDecrypterStub->method('decrypt')
            ->willReturn($this->mockAccount());

        $this->findAccountStub->method('getAccount')
            ->willReturn(null);

        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'token' => 'any_token',
            'account' => 'any_email@email.com',
            'password' => 'any_password',
            'domain' => 'any_domain'
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
            ->willReturn($this->mockAccount());

        $this->findAccountStub->method('getAccount')
            ->willThrowException(new Error());

        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'token' => 'any_token',
            'account' => 'any_email@email.com',
            'password' => 'any_password',
            'domain' => 'any_domain'
        ]);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new InternalServerError();

        $this->assertEquals(500, $httpResponse->getStatusCode());
        $this->assertInstanceOf(InternalServerError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }

    public function testShouldFindAccountHaveBeenCalledWithCorrectAccountModel()
    {
        $this->tokenDecrypterStub->method('decrypt')
            ->willReturn($this->mockAccount());

        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'token' => 'any_token',
            'account' => 'any_email@email.com',
            'password' => 'any_password',
            'domain' => 'any_domain'
        ]);

        $tokenAccount = $this->mockAccount();

        $this->findAccountStub->expects($this->once())
            ->method('getAccount')
            ->with(new FindAccountModel($tokenAccount['email'], $tokenAccount['password']));

        $this->sut->handle($httpRequest);
    }

    public function testShouldReturn400IfPasswordAccountExists()
    {
        $this->tokenDecrypterStub->method('decrypt')
            ->willReturn($this->mockAccount());

        $this->findAccountStub->method('getAccount')
            ->willReturn($this->mockAccountModel());

        $this->checkPasswordStub->method('check')
            ->willReturn(true);

        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'token' => 'any_token',
            'account' => 'any_email@email.com',
            'password' => 'any_password',
            'domain' => 'any_domain'
        ]);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new PasswordAccountExistsError($httpRequest->getBody()['account'], $httpRequest->getBody()['domain']);

        $this->assertEquals(400, $httpResponse->getStatusCode());
        $this->assertInstanceOf(PasswordAccountExistsError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }

    public function testShouldReturn500IfCheckPasswordThrows()
    {
        $this->tokenDecrypterStub->method('decrypt')
            ->willReturn($this->mockAccount());

        $this->findAccountStub->method('getAccount')
            ->willReturn($this->mockAccountModel());

        $this->checkPasswordStub->method('check')
            ->willThrowException(new Error());

        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'token' => 'any_token',
            'account' => 'any_email@email.com',
            'password' => 'any_password',
            'domain' => 'any_domain'
        ]);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new InternalServerError();

        $this->assertEquals(500, $httpResponse->getStatusCode());
        $this->assertInstanceOf(InternalServerError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }

    public function testShouldCheckPasswordHaveBeenCalledWithCorrectFindPasswordModel()
    {
        $this->tokenDecrypterStub->method('decrypt')
            ->willReturn($this->mockAccount());

        $this->findAccountStub->method('getAccount')
            ->willReturn($this->mockAccountModel());

        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'token' => 'any_token',
            'account' => 'any_email@email.com',
            'password' => 'any_password',
            'domain' => 'any_domain'
        ]);

        $this->checkPasswordStub->expects($this->once())
            ->method('check')
            ->with(new FindPasswordModel(($this->mockAccountModel())->getId(), $httpRequest->getBody()['account'], $httpRequest->getBody()['domain']));

        $this->sut->handle($httpRequest);
    }

    public function testShouldReturn500IfAddPasswordThrows()
    {
        $this->tokenDecrypterStub->method('decrypt')
            ->willReturn($this->mockAccount());

        $this->findAccountStub->method('getAccount')
            ->willReturn($this->mockAccountModel());

        $this->checkPasswordStub->method('check')
            ->willReturn(false);

        $this->addPasswordStub->method('add')
            ->willThrowException(new Error());

        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'token' => 'any_token',
            'account' => 'any_email@email.com',
            'password' => 'any_password',
            'domain' => 'any_domain'
        ]);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new InternalServerError();

        $this->assertEquals(500, $httpResponse->getStatusCode());
        $this->assertInstanceOf(InternalServerError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }
}
