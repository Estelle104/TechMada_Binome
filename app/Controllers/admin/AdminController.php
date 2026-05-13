<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;

class AdminController extends BaseController
{
    public function index()
    {
        $user = session()->get('user');
        if (!$user || ($user['role'] ?? '') !== 'admin') {
            return redirect()->to('/')->with('error', 'Acces refuse : droits insuffisants');
        }

        return view('admin/dashboard');
    }
}
