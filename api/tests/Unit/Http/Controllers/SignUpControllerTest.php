<?php

namespace Tests\Unit\Http\Controllers;

use App\Domain\Models\AccountModel;
use App\Domain\Models\AddAccountModel;
use App\Domain\Models\FindAccountModel;
use App\Domain\UseCases\AddAccount;
use App\Domain\UseCases\CheckAccount;
use App\Exceptions\AccountExistsError;
use App\Exceptions\InternalServerError;
use App\Exceptions\InvalidParamError;
use App\Exceptions\MissingParamError;
use App\Http\Controllers\SignUpController;
use App\Http\Protocols\EmailValidator;
use App\Http\Protocols\HttpRequest;
use App\Http\Protocols\TokenGenerator;
use Error;
use Tests\TestCase;

class SignUpControllerTest extends TestCase
{
    private SignUpController $sut;

    private EmailValidator $emailValidatorStub;
    private AddAccount $addAccountStub;
    private CheckAccount $checkAccountStub;
    private TokenGenerator $tokenGeneratorStub;

    public function setUp(): void
    {
        $this->emailValidatorStub = $this->createMock(EmailValidator::class);
        $this->checkAccountStub = $this->createMock(CheckAccount::class);
        $this->addAccountStub = $this->createMock(AddAccount::class);
        $this->tokenGeneratorStub = $this->createMock(TokenGenerator::class);

        $this->sut = new SignUpController(
            $this->emailValidatorStub,
            $this->checkAccountStub,
            $this->addAccountStub,
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

    private function mockFindAccountModel(): FindAccountModel
    {
        return new FindAccountModel(
            "any_name",
            "any_email",
            "any_password"
        );
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

    public function testShouldReturn400IfNoPasswordConfirmationWasProvided()
    {
        $httpRequest = new HttpRequest();

        $body = [
            'name' => 'any_name',
            'email' => 'any_email',
            'password' => 'any_password',
            'passwordConfirmation' => ''
        ];

        $httpRequest->setBody($body);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new MissingParamError('passwordConfirmation');

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
            'name' => 'any_name',
            'email' => 'any_email',
            'password' => 'any_password',
            'passwordConfirmation' => 'any_password'
        ];

        $httpRequest->setBody($body);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new InvalidParamError('email');

        $this->assertEquals(400, $httpResponse->getStatusCode());
        $this->assertInstanceOf(InvalidParamError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
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

    public function testShouldReturn400IfInvalidPasswordConfirmationWasProvided()
    {
        $httpRequest = new HttpRequest();

        $this->emailValidatorStub->method('validate')
            ->willReturn(true);

        $body = [
            'name' => 'any_name',
            'email' => 'any_email',
            'password' => 'any_password',
            'passwordConfirmation' => 'invalid_password'
        ];

        $httpRequest->setBody($body);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new InvalidParamError('passwordConfirmation');

        $this->assertEquals(400, $httpResponse->getStatusCode());
        $this->assertInstanceOf(InvalidParamError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }

    public function testShouldeReturn500IfAddAccountThrows()
    {
        $this->emailValidatorStub->method('validate')
            ->willReturn(true);

        $this->addAccountStub->method('add')
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

    public function testShouldEmailValidatorHaveBeenCalledWithCorrectAddAccountModel()
    {
        $this->emailValidatorStub->method('validate')
            ->willReturn(true);

        $httpRequest = new HttpRequest();

        $body = [
            'name' => 'any_name',
            'email' => 'any_email',
            'password' => 'any_password',
            'passwordConfirmation' => 'any_password'
        ];

        $this->addAccountStub->expects($this->once())
            ->method('add')
            ->with(new AddAccountModel($body['name'], $body['email'], $body['password']));

        $httpRequest->setBody($body);

        $this->sut->handle($httpRequest);
    }

    public function testShouldReturn400IfAccountsExists()
    {
        $this->emailValidatorStub->method('validate')
            ->willReturn(true);

        $this->addAccountStub->method('add')
            ->willReturn($this->mockAccountModel());

        $this->tokenGeneratorStub->method('generate')
            ->willThrowException(new Error());

        $this->checkAccountStub->method('verifyIfExists')
            ->willReturn(true);

        $httpRequest = new HttpRequest();

        $body = [
            'name' => 'any_name',
            'email' => 'any_email',
            'password' => 'any_password',
            'passwordConfirmation' => 'any_password'
        ];

        $httpRequest->setBody($body);

        $httpResponse = $this->sut->handle($httpRequest);

        $error = new AccountExistsError($body['email']);

        $this->assertEquals(400, $httpResponse->getStatusCode());
        $this->assertInstanceOf(AccountExistsError::class, $httpResponse->getBody());
        $this->assertEquals($error->getMessage(), $httpResponse->getBody()->getMessage());
    }

    public function testShouldReturn500IfCheckAccountAccountThrows()
    {
        $this->emailValidatorStub->method('validate')
            ->willReturn(true);

        $this->addAccountStub->method('add')
            ->willReturn($this->mockAccountModel());

        $this->tokenGeneratorStub->method('generate')
            ->willThrowException(new Error());

        $this->checkAccountStub->method('verifyIfExists')
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

    public function testShouldCheckAccountHaveBeenCalledWithCorrectFindAccountModel()
    {
        $this->emailValidatorStub->method('validate')
            ->willReturn(true);

        $this->addAccountStub->method('add')
            ->willReturn($this->mockAccountModel());

        $this->tokenGeneratorStub->method('generate')
            ->willThrowException(new Error());

        $this->checkAccountStub->expects($this->once())
            ->method('verifyIfExists')
            ->with($this->mockFindAccountModel());

        $httpRequest = new HttpRequest();

        $body = [
            'name' => 'any_name',
            'email' => 'any_email',
            'password' => 'any_password',
            'passwordConfirmation' => 'any_password'
        ];

        $httpRequest->setBody($body);

        $this->sut->handle($httpRequest);
    }

    public function testShouldReturn500IfTokenGeneratorThrows()
    {
        $this->emailValidatorStub->method('validate')
            ->willReturn(true);

        $this->addAccountStub->method('add')
            ->willReturn($this->mockAccountModel());

        $this->tokenGeneratorStub->method('generate')
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

    public function testShouldTokenGeneratorValidatorHaveBeenCalledWithCorrectAccountModel()
    {
        $accountModel = $this->mockAccountModel();

        $this->emailValidatorStub->method('validate')
            ->willReturn(true);

        $this->addAccountStub->method('add')
            ->willReturn($accountModel);

        $this->tokenGeneratorStub->expects($this->once())
            ->method('generate')
            ->with($accountModel);

        $httpRequest = new HttpRequest();

        $body = [
            'name' => 'any_name',
            'email' => 'any_email',
            'password' => 'any_password',
            'passwordConfirmation' => 'any_password'
        ];

        $httpRequest->setBody($body);

        $this->sut->handle($httpRequest);
    }

    public function testShouldReturn200OnSuccess()
    {
        $token = "VALID_TOKEN";

        $this->emailValidatorStub->method('validate')
            ->willReturn(true);

        $this->addAccountStub->method('add')
            ->willReturn($this->mockAccountModel());

        $this->tokenGeneratorStub->method('generate')
            ->willReturn($token);

        $httpRequest = new HttpRequest();

        $body = [
            'name' => 'valid_name',
            'email' => 'valid_email',
            'password' => 'valid_password',
            'passwordConfirmation' => 'valid_password'
        ];

        $httpRequest->setBody($body);

        $httpResponse = $this->sut->handle($httpRequest);

        $this->assertEquals(200, $httpResponse->getStatusCode());
        $this->assertEquals($token, $httpResponse->getBody());
    }
}
