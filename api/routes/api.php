<?php

use App\Http\Factories\SignUpControllerFactory;
use App\Http\Adapters\RouteAdapter;
use App\Http\Factories\SignInControllerFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/account/signup', function (Request $request) {
    return (new RouteAdapter())->adapt($request, (new SignUpControllerFactory)->make());
});

Route::post('/account/signin', function (Request $request) {
    return (new RouteAdapter())->adapt($request, (new SignInControllerFactory)->make());
});
