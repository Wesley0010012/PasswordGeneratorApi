<?php

namespace App\Http\Adapters;

use App\Http\Controllers\Controller;
use App\Http\Protocols\HttpRequest;
use App\Http\Protocols\HttpResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RouteAdapter implements RouteAdapterInterface
{
    private function makeJsonReturn(HttpResponse $httpResponse): string
    {
        if ($httpResponse->getStatusCode() !== 200) {
            $error = $httpResponse->getBody();

            return json_encode([
                "error" => $error->getMessage(),
            ]);
        }

        return json_encode($httpResponse->getBody());
    }

    public function adapt(Request $request, Controller $controller): Response
    {
        $httpRequest = new HttpRequest();

        $httpRequest->setBody([
            "name" => $request['name'] ?? '',
            "email" => $request['email'] ?? '',
            "password" => $request['password'] ?? '',
            "passwordConfirmation" => $request['passwordConfirmation'] ?? '',
            "token" => $request['token'] ?? '',
            "size" => $request['size'] ?? 0
        ]);

        $httpResponse = $controller->handle($httpRequest);

        return response($this->makeJsonReturn($httpResponse), $httpResponse->statusCode)->header("content-type", "text/json");
    }
}
