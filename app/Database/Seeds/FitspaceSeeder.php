<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FitspaceSeeder extends Seeder
{
    public function run()
    {
        $this->call('GestionCongeSeeder');
    }
}
