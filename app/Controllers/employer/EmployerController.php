<?php

namespace App\Controllers\employer;

use App\Controllers\BaseController;
use App\Models\CongeModel;
use App\Models\EmployeModel;
use DateTime;

class EmployerController extends BaseController
{
    public function index()
    {
        $user = session()->get('user');
        if (!$user || ($user['role'] ?? '') !== 'employe') {
            return redirect()->to('/')->with('error', 'Acces refuse : droits insuffisants');
        }

        return view('employer/dashboard');
    }

    public function profile(int $id)
    {
        $model = new EmployeModel();
        $employe = $model
            ->select('employes.*, departements.nom as departement_nom, departements.description as departement_description')
            ->join('departements', 'departements.id = employes.departement_id', 'left')
            ->where('employes.id', $id)
            ->first();

        if (!$employe) {
            return $this->response->setStatusCode(404)->setJSON([
                'message' => 'Employe introuvable.'
            ]);
        }

        return $this->response->setJSON($employe);
    }

    public function demandeConge()
    {
        $user = session()->get('user');
        if (!$user) {
            return $this->response->setStatusCode(401)->setJSON([
                'message' => 'Non authentifie.'
            ]);
        }

        $typeCongeId = (int) $this->request->getPost('type_conge_id');
        $dateDebut = $this->request->getPost('date_debut');
        $dateFin = $this->request->getPost('date_fin');
        $motif = $this->request->getPost('motif');

        if ($typeCongeId <= 0 || empty($dateDebut) || empty($dateFin)) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'type_conge_id, date_debut et date_fin sont requis.'
            ]);
        }

        $start = DateTime::createFromFormat('Y-m-d', $dateDebut);
        $end = DateTime::createFromFormat('Y-m-d', $dateFin);
        if (!$start || !$end || $start->format('Y-m-d') !== $dateDebut || $end->format('Y-m-d') !== $dateFin) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Format de date invalide. Utiliser YYYY-MM-DD.'
            ]);
        }

        if ($start > $end) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'date_debut doit etre avant ou egale a date_fin.'
            ]);
        }

        $nbJours = (int) $end->diff($start)->format('%a') + 1;

        $model = new CongeModel();
        $congeId = $model->insert([
            'employe_id' => (int) $user['id'],
            'type_conge_id' => $typeCongeId,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'nb_jours' => $nbJours,
            'motif' => $motif,
            'statut' => 'en_attente',
            'commentaire_rh' => null,
            'traite_par' => null,
            'date_traitement' => null,
        ], true);

        return $this->response->setStatusCode(201)->setJSON([
            'message' => 'Demande de conge enregistree.',
            'id' => $congeId,
            'nb_jours' => $nbJours
        ]);
    }

    public function listeConge()
    {
        $user = session()->get('user');
        if (!$user) {
            return $this->response->setStatusCode(401)->setJSON([
                'message' => 'Non authentifie.'
            ]);
        }

        $model = new CongeModel();
        $conges = $model
            ->select('conges.*, types_conge.nom as type_conge_nom')
            ->join('types_conge', 'types_conge.id = conges.type_conge_id', 'left')
            ->where('conges.employe_id', (int) $user['id'])
            ->orderBy('conges.date_debut', 'DESC')
            ->findAll();

        return $this->response->setJSON($conges);
    }

    public function annulerConge(int $id)
    {
        $user = session()->get('user');
        if (!$user) {
            return $this->response->setStatusCode(401)->setJSON([
                'message' => 'Non authentifie.'
            ]);
        }

        $model = new CongeModel();
        $conge = $model->where('id', $id)
            ->where('employe_id', (int) $user['id'])
            ->first();

        if (!$conge) {
            return $this->response->setStatusCode(404)->setJSON([
                'message' => 'Demande introuvable.'
            ]);
        }

        if ($conge['statut'] !== 'en_attente') {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Seules les demandes en attente peuvent etre annulees.'
            ]);
        }

        $model->update($id, [
            'statut' => 'annulee',
            'date_traitement' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'message' => 'Demande annulee.'
        ]);
    }
}