<?php

namespace App\Models;
use CodeIgniter\Model;

class ProduitModel extends Model {
    protected $table = 'produit';
    protected $allowedFields = ['nom', 'prix'];
}