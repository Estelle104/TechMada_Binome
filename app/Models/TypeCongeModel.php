<?php

namespace App\Models;
use CodeIgniter\Model;

class TypeCongeModel extends Model {
    protected $table = 'types_conge';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'nom',
        'jours_annuels',
        'deductible',
        'description'
    ];
}
