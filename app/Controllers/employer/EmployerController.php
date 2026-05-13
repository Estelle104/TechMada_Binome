<?php

namespace App\Controllers\employer;

use App\Controllers\BaseController;
use App\Models\CongeModel;
use App\Models\EmployeModel;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\HTTP\RedirectResponse;
use DateTime;

class EmployerController extends BaseController
{
    private BaseConnection $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    private function ensureEmploye(): ?RedirectResponse
    {
        $user = session()->get('user');
        if (!$user || ($user['role'] ?? '') !== 'employe') {
            return redirect()->to('/')->with('error', 'Acces refuse : droits insuffisants');
        }

        return null;
    }

    public function index()
    {
        $guard = $this->ensureEmploye();
        if ($guard) {
            return $guard;
        }

        $user = session()->get('user');

        $annee = (int) date('Y');
        $employeId = (int) $user['id'];

        $employe = $this->db->table('employes e')
            ->select('e.nom, e.prenom, e.email, e.role, e.date_embauche, d.nom AS departement_nom')
            ->join('departements d', 'd.id = e.departement_id', 'left')
            ->where('e.id', $employeId)
            ->get()
            ->getRowArray();

        $statCounts = ['en_attente' => 0, 'approuvee' => 0, 'refusee' => 0, 'annulee' => 0];
        $rows = $this->db->table('conges')
            ->select('statut, COUNT(*) as total')
            ->where('employe_id', $employeId)
            ->groupBy('statut')
            ->get()
            ->getResultArray();
        foreach ($rows as $row) {
            $stat = (string) ($row['statut'] ?? '');
            if ($stat !== '' && array_key_exists($stat, $statCounts)) {
                $statCounts[$stat] = (int) $row['total'];
            }
        }

        $soldes = $this->db->table('soldes s')
            ->select('s.id, s.type_conge_id, s.jours_attribues, s.jours_pris, t.nom AS type_conge_nom')
            ->join('types_conge t', 't.id = s.type_conge_id', 'left')
            ->where('s.employe_id', $employeId)
            ->where('s.annee', $annee)
            ->orderBy('t.nom', 'ASC')
            ->get()
            ->getResultArray();

        $soldesView = [];
        $annualRestants = 0;
        $annualAttribues = 0;
        foreach ($soldes as $s) {
            $attribues = (int) ($s['jours_attribues'] ?? 0);
            $pris = (int) ($s['jours_pris'] ?? 0);
            $restants = max(0, $attribues - $pris);
            $typeNom = (string) ($s['type_conge_nom'] ?? '');
            $soldesView[] = [
                'type_nom' => $typeNom,
                'attribues' => $attribues,
                'pris' => $pris,
                'restants' => $restants,
            ];

            if (stripos($typeNom, 'annuel') !== false) {
                $annualRestants = $restants;
                $annualAttribues = $attribues;
            }
        }

        if ($annualAttribues === 0 && !empty($soldesView)) {
            $annualAttribues = $soldesView[0]['attribues'];
            $annualRestants = $soldesView[0]['restants'];
        }

        $recentDemandes = $this->db->table('conges c')
            ->select('c.id, c.date_debut, c.date_fin, c.nb_jours, c.statut, t.nom AS type_conge_nom')
            ->join('types_conge t', 't.id = c.type_conge_id', 'left')
            ->where('c.employe_id', $employeId)
            ->orderBy('c.date_debut', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        return view('employer/dashboard', [
            'annee' => $annee,
            'employe' => $employe,
            'counts' => $statCounts,
            'soldes' => $soldesView,
            'annual' => [
                'attribues' => $annualAttribues,
                'restants' => $annualRestants,
            ],
            'recentDemandes' => $recentDemandes,
        ]);
    }

    public function create()
    {
        $guard = $this->ensureEmploye();
        if ($guard) {
            return $guard;
        }

        $user = session()->get('user');

        $annee = (int) date('Y');
        $employeId = (int) $user['id'];

        $soldes = $this->db->table('soldes s')
            ->select('s.type_conge_id, s.jours_attribues, s.jours_pris, t.nom AS type_conge_nom')
            ->join('types_conge t', 't.id = s.type_conge_id', 'left')
            ->where('s.employe_id', $employeId)
            ->where('s.annee', $annee)
            ->orderBy('t.nom', 'ASC')
            ->get()
            ->getResultArray();

        $types = [];
        foreach ($soldes as $s) {
            $attribues = (int) ($s['jours_attribues'] ?? 0);
            $pris = (int) ($s['jours_pris'] ?? 0);
            $types[] = [
                'id' => (int) $s['type_conge_id'],
                'nom' => (string) ($s['type_conge_nom'] ?? ''),
                'restants' => max(0, $attribues - $pris),
                'attribues' => $attribues,
            ];
        }

        return view('employer/create', [
            'annee' => $annee,
            'types' => $types,
        ]);
    }

    public function mesConge()
    {
        $guard = $this->ensureEmploye();
        if ($guard) {
            return $guard;
        }

        $user = session()->get('user');

        $statut = $this->request->getGet('statut') ?? 'all';
        $employeId = (int) $user['id'];

        $builder = $this->db->table('conges c')
            ->select('c.id, c.date_debut, c.date_fin, c.nb_jours, c.statut, c.commentaire_rh, t.nom AS type_conge_nom')
            ->join('types_conge t', 't.id = c.type_conge_id', 'left')
            ->where('c.employe_id', $employeId)
            ->orderBy('c.date_debut', 'DESC');

        if ($statut !== 'all') {
            $builder->where('c.statut', $statut);
        }

        $conges = $builder->get()->getResultArray();

        return view('employer/index', [
            'statut' => $statut,
            'conges' => $conges,
        ]);
    }

    public function profil()
    {
        $guard = $this->ensureEmploye();
        if ($guard) {
            return $guard;
        }

        $user = session()->get('user');

        $employeId = (int) $user['id'];
        $employe = $this->db->table('employes e')
            ->select('e.nom, e.prenom, e.email, e.role, e.date_embauche, e.actif, d.nom AS departement_nom, d.description AS departement_description')
            ->join('departements d', 'd.id = e.departement_id', 'left')
            ->where('e.id', $employeId)
            ->get()
            ->getRowArray();

        return view('employer/profile', [
            'employe' => $employe,
        ]);
    }

    public function editProfil()
    {
        $guard = $this->ensureEmploye();
        if ($guard) {
            return $guard;
        }

        $user = session()->get('user');
        $employeId = (int) $user['id'];
        $employe = $this->db->table('employes')
            ->select('id, nom, prenom, email')
            ->where('id', $employeId)
            ->get()
            ->getRowArray();

        return view('employer/profile_edit', [
            'employe' => $employe,
        ]);
    }

    public function updateProfil()
    {
        $guard = $this->ensureEmploye();
        if ($guard) {
            return $guard;
        }

        $user = session()->get('user');
        $employeId = (int) $user['id'];

        $nom = trim((string) $this->request->getPost('nom'));
        $prenom = trim((string) $this->request->getPost('prenom'));
        $email = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('mot_de_passe');
        $passwordConfirm = (string) $this->request->getPost('mot_de_passe_confirm');

        if ($nom === '' || $prenom === '' || $email === '') {
            return redirect()->back()->withInput()->with('error', 'Nom, prenom et email sont requis.');
        }

        $emailExists = $this->db->table('employes')
            ->where('email', $email)
            ->where('id !=', $employeId)
            ->countAllResults();
        if ($emailExists > 0) {
            return redirect()->back()->withInput()->with('error', 'Cet email est deja utilise.');
        }

        if ($password !== '' && $password !== $passwordConfirm) {
            return redirect()->back()->withInput()->with('error', 'Les mots de passe ne correspondent pas.');
        }

        $data = [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
        ];
        if ($password !== '') {
            $data['mot_de_passe'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $model = new EmployeModel();
        $model->update($employeId, $data);

        session()->set('user', [
            'id' => $employeId,
            'email' => $email,
            'role' => $user['role'],
        ]);

        return redirect()->to('/employer/profil')->with('success', 'Profil mis a jour.');
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
        $accept = strtolower((string) $this->request->getHeaderLine('Accept'));
        $wantsJson = $this->request->isAJAX() || str_contains($accept, 'application/json');
        if (!$user) {
            if ($wantsJson) {
                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Non authentifie.'
                ]);
            }

            return redirect()->to('/')->with('error', 'Veuillez vous connecter.');
        }

        $typeCongeId = (int) $this->request->getPost('type_conge_id');
        $dateDebut = $this->request->getPost('date_debut');
        $dateFin = $this->request->getPost('date_fin');
        $motif = $this->request->getPost('motif');

        if ($typeCongeId <= 0 || empty($dateDebut) || empty($dateFin)) {
            if ($wantsJson) {
                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'type_conge_id, date_debut et date_fin sont requis.'
                ]);
            }

            return redirect()->back()->withInput()->with('error', 'Type, date de debut et date de fin sont requis.');
        }

        $start = DateTime::createFromFormat('Y-m-d', $dateDebut);
        $end = DateTime::createFromFormat('Y-m-d', $dateFin);
        if (!$start || !$end || $start->format('Y-m-d') !== $dateDebut || $end->format('Y-m-d') !== $dateFin) {
            if ($wantsJson) {
                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'Format de date invalide. Utiliser YYYY-MM-DD.'
                ]);
            }

            return redirect()->back()->withInput()->with('error', 'Format de date invalide. Utiliser YYYY-MM-DD.');
        }

        if ($start > $end) {
            if ($wantsJson) {
                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'date_debut doit etre avant ou egale a date_fin.'
                ]);
            }

            return redirect()->back()->withInput()->with('error', 'La date de debut doit etre avant ou egale a la date de fin.');
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

        if ($wantsJson) {
            return $this->response->setStatusCode(201)->setJSON([
                'message' => 'Demande de conge enregistree.',
                'id' => $congeId,
                'nb_jours' => $nbJours
            ]);
        }

        return redirect()->to('/employer/conges/mes')->with('success', 'Demande de conge enregistree.');
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
        $accept = strtolower((string) $this->request->getHeaderLine('Accept'));
        $wantsJson = $this->request->isAJAX() || str_contains($accept, 'application/json');
        if (!$user) {
            if ($wantsJson) {
                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Non authentifie.'
                ]);
            }

            return redirect()->to('/')->with('error', 'Veuillez vous connecter.');
        }

        $model = new CongeModel();
        $conge = $model->where('id', $id)
            ->where('employe_id', (int) $user['id'])
            ->first();

        if (!$conge) {
            if ($wantsJson) {
                return $this->response->setStatusCode(404)->setJSON([
                    'message' => 'Demande introuvable.'
                ]);
            }

            return redirect()->to('/employer/conges/mes')->with('error', 'Demande introuvable.');
        }

        if ($conge['statut'] !== 'en_attente') {
            if ($wantsJson) {
                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'Seules les demandes en attente peuvent etre annulees.'
                ]);
            }

            return redirect()->to('/employer/conges/mes')->with('error', 'Seules les demandes en attente peuvent etre annulees.');
        }

        $model->update($id, [
            'statut' => 'annulee',
            'date_traitement' => date('Y-m-d H:i:s')
        ]);

        if ($wantsJson) {
            return $this->response->setJSON([
                'message' => 'Demande annulee.'
            ]);
        }

        return redirect()->to('/employer/conges/mes')->with('success', 'Demande annulee.');
    }
}