<?php

namespace App\Controllers;

use App\Models\EmployeModel;

class Home extends BaseController
{
    public function index(): string
    {
        return view('login');
    }

    public function login() {
        $email = $this->request->getPost('email') ?? $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $model = new EmployeModel();
        $user = $model->where('email', $email)->first();

        if (!$user || (int) ($user['actif'] ?? 0) !== 1) {
            return redirect()->to('/')->with('error', 'Nom d’utilisateur ou mot de passe incorrect.');
        }

        // mot_de_passe stored as hash
        if (!password_verify($password, $user['mot_de_passe'])) {
            return redirect()->to('/')->with('error', 'Nom d’utilisateur ou mot de passe incorrect.');
        }

        session()->set('user', [
            'id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
        ]);

        if ($user['role'] === 'rh') {
            return redirect()->to('/rh')->with('success', 'Connexion reussie.');
        }

        if ($user['role'] === 'admin') {
            return redirect()->to('/admin')->with('success', 'Connexion reussie.');
        }

        return redirect()->to('/employer')->with('success', 'Connexion reussie.');
    }

    public function logout()
    {
        session()->remove('user');
        return redirect()->to('/')->with('success', 'Deconnexion reussie.');
    }
    
}
