<?php

namespace App\Models;
use CodeIgniter\Model;

class EmployeModel extends Model {
    protected $table = 'employes';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'nom',
        'prenom',
        'email',
        'mot_de_passe',
        'role',
        'date_embauche',
        'actif',
        'departement_id'
    ];
}
