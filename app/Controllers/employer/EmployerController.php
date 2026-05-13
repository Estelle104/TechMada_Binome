<?php

namespace App\Controllers\employer;

use App\Controllers\BaseController;

class EmployerController extends BaseController
{
    public function index()
    {
        return view('employer/dashboard');
    }
}