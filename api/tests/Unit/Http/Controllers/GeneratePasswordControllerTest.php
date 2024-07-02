<?php

namespace Tests\Unit\Http\Controllers;

use App\Exceptions\MissingParamError;
use App\Http\Controllers\GeneratePasswordController;
use App\Http\Protocols\HttpRequest;
use Tests\TestCase;

class GeneratePasswordControllerTest extends TestCase
{
    private GeneratePasswordController $sut;

    public function setUp(): void
    {
        $this->sut = new GeneratePasswordController();
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
}
