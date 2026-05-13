<?php

namespace App\Controllers\respRH;

use App\Controllers\BaseController;
use App\Models\CongeModel;
use CodeIgniter\Database\BaseConnection;

class RespRhController extends BaseController
{
    private CongeModel $congeModel;
    private BaseConnection $db;

    public function __construct()
    {
        $this->congeModel = new CongeModel();
        $this->db = db_connect();
    }

    public function index()
    {
        // Ensure the current user is RH (defensive check, not only filter)
        $sessionUser = session()->get('user');
        if (!$sessionUser || ($sessionUser['role'] ?? '') !== 'rh') {
            return redirect()->to('/')->with('error', 'Acces refuse : droits insuffisants');
        }

        $statut = $this->request->getGet('statut') ?? 'all';
        $departementId = $this->request->getGet('departement_id') ?? 'all';
        $annee = (int) date('Y');

        $builder = $this->db->table('conges c')
            ->select('c.id, c.employe_id, c.type_conge_id, c.date_debut, c.date_fin, c.nb_jours, c.motif, c.statut, c.commentaire_rh, c.traite_par, c.date_traitement, c.created_at')
            ->select('e.nom AS employe_nom, e.prenom AS employe_prenom, d.id AS departement_id, d.nom AS departement_nom')
            ->select('t.nom AS type_conge_nom')
            ->select('s.id AS solde_id, s.jours_attribues, s.jours_pris')
            ->select('(COALESCE(s.jours_attribues, 0) - COALESCE(s.jours_pris, 0)) AS jours_restants', false)
            ->join('employes e', 'e.id = c.employe_id', 'inner')
            ->join('departements d', 'd.id = e.departement_id', 'left')
            ->join('types_conge t', 't.id = c.type_conge_id', 'inner')
            ->join('soldes s', 's.employe_id = c.employe_id AND s.type_conge_id = c.type_conge_id AND s.annee = ' . $annee, 'left');

        if ($statut !== 'all') {
            $builder->where('c.statut', $statut);
        }

        if ($departementId !== 'all') {
            $builder->where('d.id', (int) $departementId);
        }

        $demandes = $builder->orderBy('c.created_at', 'DESC')->get()->getResultArray();

        $departements = $this->db->table('departements')->select('id, nom')->orderBy('nom', 'ASC')->get()->getResultArray();

        $total = $this->countDemandes(null, $departementId);
        $enAttente = $this->countDemandes('en_attente', $departementId);
        $approuvee = $this->countDemandes('approuvee', $departementId);
        $refusee = $this->countDemandes('refusee', $departementId);

        return view('respRH/index', [
            'demandes' => $demandes,
            'departements' => $departements,
            'filters' => [
                'statut' => $statut,
                'departement_id' => $departementId,
            ],
            'counts' => [
                'total' => $total,
                'en_attente' => $enAttente,
                'approuvee' => $approuvee,
                'refusee' => $refusee,
            ],
        ]);
    }

    public function approve(int $id)
    {
        $sessionUser = session()->get('user');
        if (!$sessionUser || ($sessionUser['role'] ?? '') !== 'rh') {
            return redirect()->to('/')->with('error', 'Acces refuse : droits insuffisants');
        }

        $demande = $this->findDemande($id);
        if (!$demande) {
            return redirect()->to('/rh')->with('error', 'Demande introuvable.');
        }

        return view('respRH/process_form', [
            'demande' => $demande,
            'mode' => 'approve',
        ]);
    }

    public function reject(int $id)
    {
        $sessionUser = session()->get('user');
        if (!$sessionUser || ($sessionUser['role'] ?? '') !== 'rh') {
            return redirect()->to('/')->with('error', 'Acces refuse : droits insuffisants');
        }

        $demande = $this->findDemande($id);
        if (!$demande) {
            return redirect()->to('/rh')->with('error', 'Demande introuvable.');
        }

        return view('respRH/process_form', [
            'demande' => $demande,
            'mode' => 'reject',
        ]);
    }

    public function approve_post(int $id)
    {
        $rh = session()->get('user');
        if (!$rh || ($rh['role'] ?? '') !== 'rh') {
            return redirect()->to('/')->with('error', 'Acces refuse : droits insuffisants');
        }
        $commentaire = trim((string) $this->request->getPost('commentaire_rh'));

        $demande = $this->findDemande($id);
        if (!$demande) {
            return redirect()->to('/rh')->with('error', 'Demande introuvable.');
        }

        if ($demande['statut'] !== 'en_attente') {
            return redirect()->to('/rh')->with('error', 'Cette demande est deja traitee.');
        }

        $joursRestants = (int) ($demande['jours_restants'] ?? 0);
        $nbJours = (int) $demande['nb_jours'];
        if ($joursRestants < $nbJours) {
            return redirect()->to('/rh/reject/' . $id)->with('error', 'Solde insuffisant pour approuver cette demande.');
        }

        $this->db->transBegin();

        $this->congeModel->update($id, [
            'statut' => 'approuvee',
            'commentaire_rh' => $commentaire !== '' ? $commentaire : null,
            'traite_par' => (int) ($rh['id'] ?? 0),
            'date_traitement' => date('Y-m-d H:i:s'),
        ]);

        if (!isset($demande['solde_id']) || !$demande['solde_id']) {
            $this->db->transRollback();
            return redirect()->to('/rh')->with('error', 'Solde non initialise pour cette demande.');
        }

        $this->db->table('soldes')
            ->where('id', (int) $demande['solde_id'])
            ->set('jours_pris', 'jours_pris + ' . $nbJours, false)
            ->update();

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return redirect()->to('/rh')->with('error', 'Echec lors de la validation de la demande.');
        }

        $this->db->transCommit();

        return redirect()->to('/rh')->with('success', 'Demande approuvee. Le solde a ete mis a jour automatiquement.');
    }

    public function reject_post(int $id)
    {
        $rh = session()->get('user');
        if (!$rh || ($rh['role'] ?? '') !== 'rh') {
            return redirect()->to('/')->with('error', 'Acces refuse : droits insuffisants');
        }
        $commentaire = trim((string) $this->request->getPost('commentaire_rh'));

        $demande = $this->findDemande($id);
        if (!$demande) {
            return redirect()->to('/rh')->with('error', 'Demande introuvable.');
        }

        if ($demande['statut'] !== 'en_attente') {
            return redirect()->to('/rh')->with('error', 'Cette demande est deja traitee.');
        }

        $this->congeModel->update($id, [
            'statut' => 'refusee',
            'commentaire_rh' => $commentaire !== '' ? $commentaire : 'Refus par le responsable RH.',
            'traite_par' => (int) ($rh['id'] ?? 0),
            'date_traitement' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/rh')->with('success', 'Demande refusee.');
    }

    private function findDemande(int $id): ?array
    {
        $annee = (int) date('Y');
        $row = $this->db->table('conges c')
            ->select('c.*, e.nom AS employe_nom, e.prenom AS employe_prenom, d.nom AS departement_nom, t.nom AS type_conge_nom')
            ->select('s.id AS solde_id, s.jours_attribues, s.jours_pris')
            ->select('(COALESCE(s.jours_attribues, 0) - COALESCE(s.jours_pris, 0)) AS jours_restants', false)
            ->join('employes e', 'e.id = c.employe_id', 'inner')
            ->join('departements d', 'd.id = e.departement_id', 'left')
            ->join('types_conge t', 't.id = c.type_conge_id', 'inner')
            ->join('soldes s', 's.employe_id = c.employe_id AND s.type_conge_id = c.type_conge_id AND s.annee = ' . $annee, 'left')
            ->where('c.id', $id)
            ->get()
            ->getRowArray();

        return $row ?: null;
    }

    private function countDemandes(?string $statut, string $departementId): int
    {
        $builder = $this->db->table('conges')
            ->join('employes e', 'e.id = conges.employe_id', 'inner');

        if ($departementId !== 'all') {
            $builder->where('e.departement_id', (int) $departementId);
        }

        if ($statut !== null) {
            $builder->where('conges.statut', $statut);
        }

        return $builder->countAllResults();
    }
}
