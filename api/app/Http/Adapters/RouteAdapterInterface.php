<?php

namespace App\Http\Adapters;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Controllers\Controller;

interface RouteAdapterInterface
{
    public function adapt(Request $request, Controller $controller): Response;
}
