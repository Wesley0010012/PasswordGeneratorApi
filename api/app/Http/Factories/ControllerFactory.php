<?php

namespace App\Http\Factories;

use App\Http\Controllers\Controller;

interface ControllerFactory
{
    public function make(): Controller;
}
