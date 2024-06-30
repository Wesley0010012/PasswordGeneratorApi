<?php

namespace Tests\Unit\Http\Controllers;

use App\Domain\Models\AccountModel;
use App\Domain\Models\FindAccountModel;
use App\Domain\UseCases\FindAccount;
use App\Exceptions\InternalServerError;
use App\Exceptions\InvalidParamError;
use App\Exceptions\MissingParamError;
use App\Exceptions\UnauthenticatedError;
use App\Http\Controllers\SignInController;
use App\Http\Protocols\EmailValidator;
use App\Http\Protocols\HttpRequest;
use App\Http\Protocols\TokenGenerator;
use Error;
use Tests\TestCase;

class SignInControllerTest extends TestCase
{
    private SignInController $sut;
    private EmailValidator $emailValidatorStub;
    private FindAccount $findAccountStub;
    private TokenGenerator $tokenGeneratorStub;

    public function setUp(): void
    {
        $this->emailValidatorStub = $this->createMock(EmailValidator::class);
        $this->findAccountStub = $this->createMock(FindAccount::class);
        $this->tokenGeneratorStub = $this->createMock(TokenGenerator::class);

        $this->sut = new SignInController(
            $this->emailValidatorStub,
            $this->findAccountStub,
            $this->tokenGeneratorStub
        );
    }

    private function mockAccountModel(): AccountModel
    {
        $accountModel =  new AccountModel();
        $accountModel->setId(9999);
        $accountModel->setName("valid_name");
        $accountModel->setEmail("valid_email");
        $accountModel->setPassword("valid_password");

        return $accountModel;
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
            'email' => 'any_email',
            'password' => 'any_password'
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
            'email' => 'any_email',
            'password' => 'any_password'
        ];

        $httpRequest->setBody($body);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new InternalServerError();

        $this->assertEquals(500, $httpResponse->getStatusCode());
        $this->assertInstanceOf(InternalServerError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }

    public function testShouldReturn400IfAccountWasNotFounded()
    {
        $this->emailValidatorStub->method('validate')
            ->willReturn(true);

        $this->findAccountStub->method('getAccount')
            ->willReturn(null);

        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'email' => 'email',
            'password' => 'any_password'
        ]);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new UnauthenticatedError($httpRequest->getBody()['email']);

        $this->assertEquals(400, $httpResponse->getStatusCode());
        $this->assertInstanceOf(UnauthenticatedError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }

    public function testShouldReturn500IfFindAccountThrows()
    {
        $this->emailValidatorStub->method('validate')
            ->willReturn(true);

        $this->findAccountStub->method('getAccount')
            ->willThrowException(new Error());

        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'email' => 'email',
            'password' => 'any_password'
        ]);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new InternalServerError();

        $this->assertEquals(500, $httpResponse->getStatusCode());
        $this->assertInstanceOf(InternalServerError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }

    public function testShouldFindAccountHaveBeenCalledWithCorrectData()
    {
        $this->emailValidatorStub->method('validate')
            ->willReturn(true);

        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'email' => 'email',
            'password' => 'any_password'
        ]);

        $this->findAccountStub->expects($this->once())
            ->method('getAccount')
            ->with(new FindAccountModel($httpRequest->getBody()['email'], $httpRequest->getBody()['password']));

        $this->sut->handle($httpRequest);
    }

    public function testShouldReturn500IfTokenGeneratorThrows()
    {
        $this->emailValidatorStub->method('validate')
            ->willReturn(true);

        $this->findAccountStub->method('getAccount')
            ->willReturn($this->mockAccountModel());

        $this->tokenGeneratorStub->method('generate')
            ->willThrowException(new Error());

        $httpRequest = new HttpRequest();
        $httpRequest->setBody([
            'email' => 'email',
            'password' => 'any_password'
        ]);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new InternalServerError();

        $this->assertEquals(500, $httpResponse->getStatusCode());
        $this->assertInstanceOf(InternalServerError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }
}
