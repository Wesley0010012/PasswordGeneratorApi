<?php

namespace App\Http\Controllers;

use App\Http\Protocols\HttpRequest;
use App\Http\Protocols\HttpResponse;

abstract class Controller
{
    abstract public function handle(HttpRequest $httpRequest): HttpResponse;
}
