<?php

namespace App\Controllers;
use App\Models\ProduitModel;

class Produit extends BaseController
{
    public function show($id) {
        $model = new ProduitModel();
        $produit = $model->find($id);
        if ($produit) {
            return "Produit : " . $produit['nom'] . ", Prix : " . $produit['prix'];
        } else {
            return "Produit non trouvé";
        }
    }
    
    public function index() {
        $model = new ProduitModel();
        $data['produits'] = $model->findAll();
        return view('produits', $data);
    }
}