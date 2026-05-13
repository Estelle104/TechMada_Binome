<?php

namespace App\Models;
use CodeIgniter\Model;

class SoldeModel extends Model {
    protected $table = 'soldes';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'employe_id',
        'type_conge_id',
        'jours_attribues',
        'jours_pris',
        'annee'
    ];
}
