<?php

namespace Tests\Unit\Http\Controllers;

use App\Exceptions\MissingParamError;
use App\Http\Controllers\SavePasswordController;
use App\Http\Protocols\HttpRequest;
use Tests\TestCase;

class SavePasswordControllerTest extends TestCase
{
    private SavePasswordController $sut;

    public function setUp(): void
    {
        $this->sut = new SavePasswordController();
    }

    public function testEnsureCorrectInstance()
    {
        $this->assertInstanceOf(SavePasswordController::class, $this->sut);
    }

    public function testEnsureReturn400IfNoTokenWasProvided()
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
}
