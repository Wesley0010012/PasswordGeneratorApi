<?php

namespace Tests\Unit\Http\Controllers;

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

        $this->assertEquals(400, $httpResponse->getStatusCode());
        $this->assertEquals('missing param: name', $httpResponse->getBody());
    }
}
