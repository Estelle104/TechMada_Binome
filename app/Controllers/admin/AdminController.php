<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\CongeModel;
use App\Models\DepartementModel;
use App\Models\EmployeModel;
use App\Models\SoldeModel;
use App\Models\TypeCongeModel;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\HTTP\RedirectResponse;

class AdminController extends BaseController
{
    private BaseConnection $db;
    private EmployeModel $employeModel;
    private DepartementModel $departementModel;
    private TypeCongeModel $typeCongeModel;
    private SoldeModel $soldeModel;
    private CongeModel $congeModel;

    public function __construct()
    {
        $this->db = db_connect();
        $this->employeModel = new EmployeModel();
        $this->departementModel = new DepartementModel();
        $this->typeCongeModel = new TypeCongeModel();
        $this->soldeModel = new SoldeModel();
        $this->congeModel = new CongeModel();
    }

    private function ensureAdmin(): ?RedirectResponse
    {
        $user = session()->get('user');
        if (!$user || ($user['role'] ?? '') !== 'admin') {
            return redirect()->to('/')->with('error', 'Acces refuse : droits insuffisants');
        }

        return null;
    }

    public function index()
    {
        $guard = $this->ensureAdmin();
        if ($guard) {
            return $guard;
        }

        $today = date('Y-m-d');
        $month = date('Y-m');
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');

        $employesActifs = $this->db->table('employes')->where('actif', 1)->countAllResults();
        $demandesEnAttente = $this->db->table('conges')->where('statut', 'en_attente')->countAllResults();
        $approuveesMois = $this->db->table('conges')
            ->where('statut', 'approuvee')
            ->where("strftime('%Y-%m', date_traitement) = '$month'", null, false)
            ->countAllResults();
        $departements = $this->db->table('departements')->countAllResults();
        $absents = $this->db->table('conges')
            ->where('statut', 'approuvee')
            ->where('date_debut <=', $monthEnd)
            ->where('date_fin >=', $monthStart)
            ->countAllResults();

        $recentDemandes = $this->db->table('conges c')
            ->select('c.id, c.nb_jours, c.statut, e.nom, e.prenom, t.nom AS type_conge_nom')
            ->join('employes e', 'e.id = c.employe_id', 'left')
            ->join('types_conge t', 't.id = c.type_conge_id', 'left')
            ->orderBy('c.date_debut', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        $absentsList = $this->db->table('conges c')
            ->select('e.nom, e.prenom, t.nom AS type_conge_nom, c.date_fin')
            ->join('employes e', 'e.id = c.employe_id', 'left')
            ->join('types_conge t', 't.id = c.type_conge_id', 'left')
            ->where('c.statut', 'approuvee')
            ->where('c.date_debut <=', $monthEnd)
            ->where('c.date_fin >=', $monthStart)
            ->limit(3)
            ->get()
            ->getResultArray();

        $chartYear = (int) date('Y');
        $yearStart = $chartYear . '-01-01';
        $yearEnd = $chartYear . '-12-31';
        $chartLabels = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre'];
        $chartLabelsDays = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        $countsByMonth = array_fill(1, 12, 0);
        $daysByMonth = array_fill(1, 12, 0);
        $countByDays = array_fill(0, 7, 0);

        $chartRows = $this->db->table('conges c')
            ->select("strftime('%m', c.date_debut) AS mois", false)
            ->select('COUNT(*) AS total', false)
            ->select('SUM(c.nb_jours) AS jours', false)
            ->where("strftime('%Y', c.date_debut) = '$chartYear'", null, false)
            ->groupBy('mois')
            ->get()
            ->getResultArray();

        foreach ($chartRows as $row) {
            $month = (int) ($row['mois'] ?? 0);
            if ($month >= 1 && $month <= 12) {
                $countsByMonth[$month] = (int) ($row['total'] ?? 0);
                $daysByMonth[$month] = (int) ($row['jours'] ?? 0);
            }
        }

        $congeRows = $this->db->table('conges')
            ->select('date_debut, date_fin')
            ->where('date_debut <=', $yearEnd)
            ->where('date_fin >=', $yearStart)
            ->get()
            ->getResultArray();

        foreach ($congeRows as $row) {
            $rangeStart = (string) ($row['date_debut'] ?? '');
            $rangeEnd = (string) ($row['date_fin'] ?? '');
            if ($rangeStart === '' || $rangeEnd === '') {
                continue;
            }

            if ($rangeStart < $yearStart) {
                $rangeStart = $yearStart;
            }
            if ($rangeEnd > $yearEnd) {
                $rangeEnd = $yearEnd;
            }

            $start = new \DateTime($rangeStart);
            $end = new \DateTime($rangeEnd);
            $end->modify('+1 day');

            while ($start < $end) {
                $weekday = (int) $start->format('w');
                $countByDays[$weekday]++;
                $start->modify('+1 day');
            }
        }

        return view('admin/dashboard', [
            'metrics' => [
                'employes_actifs' => $employesActifs,
                'demandes_en_attente' => $demandesEnAttente,
                'approuvees_mois' => $approuveesMois,
                'departements' => $departements,
                'absents' => $absents,
            ],
            'recentDemandes' => $recentDemandes,
            'absentsList' => $absentsList,
            'chart' => [
                'year' => $chartYear,
                'labels' => $chartLabels,
                'labelsDays' => $chartLabelsDays,
                'counts' => array_values($countsByMonth),
                'jours' => array_values($daysByMonth),
                'joursParSemaine' => array_values($countByDays),
            ],
        ]);
    }

    public function employes()
    {
        $guard = $this->ensureAdmin();
        if ($guard) {
            return $guard;
        }

        $editId = (int) ($this->request->getGet('edit') ?? 0);
        $editEmploye = null;
        if ($editId > 0) {
            $editEmploye = $this->employeModel->find($editId);
        }

        $employes = $this->db->table('employes e')
            ->select('e.*, d.nom AS departement_nom')
            ->join('departements d', 'd.id = e.departement_id', 'left')
            ->orderBy('e.nom', 'ASC')
            ->get()
            ->getResultArray();

        $departements = $this->departementModel->orderBy('nom', 'ASC')->findAll();

        return view('admin/employes', [
            'employes' => $employes,
            'departements' => $departements,
            'editEmploye' => $editEmploye,
        ]);
    }

    public function createEmploye()
    {
        $guard = $this->ensureAdmin();
        if ($guard) {
            return $guard;
        }

        $nom = trim((string) $this->request->getPost('nom'));
        $prenom = trim((string) $this->request->getPost('prenom'));
        $email = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('mot_de_passe');
        $role = (string) $this->request->getPost('role');
        $departementId = $this->request->getPost('departement_id');
        $dateEmbauche = (string) $this->request->getPost('date_embauche');

        if ($nom === '' || $prenom === '' || $email === '' || $password === '' || $role === '' || $dateEmbauche === '') {
            return redirect()->back()->withInput()->with('error', 'Tous les champs obligatoires doivent etre remplis.');
        }

        $this->employeModel->insert([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'mot_de_passe' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
            'date_embauche' => $dateEmbauche,
            'actif' => 1,
            'departement_id' => $departementId !== '' ? (int) $departementId : null,
        ]);

        return redirect()->to('/admin/employes')->with('success', 'Employe cree avec succes.');
    }

    public function updateEmploye(int $id)
    {
        $guard = $this->ensureAdmin();
        if ($guard) {
            return $guard;
        }

        $nom = trim((string) $this->request->getPost('nom'));
        $prenom = trim((string) $this->request->getPost('prenom'));
        $email = trim((string) $this->request->getPost('email'));
        $role = (string) $this->request->getPost('role');
        $departementId = $this->request->getPost('departement_id');
        $dateEmbauche = (string) $this->request->getPost('date_embauche');
        $actif = (int) $this->request->getPost('actif');
        $password = (string) $this->request->getPost('mot_de_passe');

        if ($nom === '' || $prenom === '' || $email === '' || $role === '' || $dateEmbauche === '') {
            return redirect()->back()->withInput()->with('error', 'Tous les champs obligatoires doivent etre remplis.');
        }

        $data = [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'role' => $role,
            'date_embauche' => $dateEmbauche,
            'actif' => $actif === 1 ? 1 : 0,
            'departement_id' => $departementId !== '' ? (int) $departementId : null,
        ];

        if ($password !== '') {
            $data['mot_de_passe'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->employeModel->update($id, $data);

        return redirect()->to('/admin/employes')->with('success', 'Employe mis a jour.');
    }

    public function deactivateEmploye(int $id)
    {
        $guard = $this->ensureAdmin();
        if ($guard) {
            return $guard;
        }

        $this->employeModel->update($id, ['actif' => 0]);

        return redirect()->to('/admin/employes')->with('success', 'Employe desactive.');
    }

    public function departements()
    {
        $guard = $this->ensureAdmin();
        if ($guard) {
            return $guard;
        }

        $editId = (int) ($this->request->getGet('edit') ?? 0);
        $editDepartement = null;
        if ($editId > 0) {
            $editDepartement = $this->departementModel->find($editId);
        }

        $departements = $this->departementModel->orderBy('nom', 'ASC')->findAll();

        return view('admin/departements', [
            'departements' => $departements,
            'editDepartement' => $editDepartement,
        ]);
    }

    public function saveDepartement()
    {
        $guard = $this->ensureAdmin();
        if ($guard) {
            return $guard;
        }

        $nom = trim((string) $this->request->getPost('nom'));
        $description = trim((string) $this->request->getPost('description'));
        if ($nom === '') {
            return redirect()->back()->withInput()->with('error', 'Le nom du departement est requis.');
        }

        $this->departementModel->insert([
            'nom' => $nom,
            'description' => $description !== '' ? $description : null,
        ]);

        return redirect()->to('/admin/departements')->with('success', 'Departement cree.');
    }

    public function updateDepartement(int $id)
    {
        $guard = $this->ensureAdmin();
        if ($guard) {
            return $guard;
        }

        $nom = trim((string) $this->request->getPost('nom'));
        $description = trim((string) $this->request->getPost('description'));
        if ($nom === '') {
            return redirect()->back()->withInput()->with('error', 'Le nom du departement est requis.');
        }

        $this->departementModel->update($id, [
            'nom' => $nom,
            'description' => $description !== '' ? $description : null,
        ]);

        return redirect()->to('/admin/departements')->with('success', 'Departement mis a jour.');
    }

    public function deleteDepartement(int $id)
    {
        $guard = $this->ensureAdmin();
        if ($guard) {
            return $guard;
        }

        $used = $this->db->table('employes')->where('departement_id', $id)->countAllResults();
        if ($used > 0) {
            return redirect()->to('/admin/departements')->with('error', 'Impossible de supprimer un departement utilise.');
        }

        $this->departementModel->delete($id);
        return redirect()->to('/admin/departements')->with('success', 'Departement supprime.');
    }

    public function typesConge()
    {
        $guard = $this->ensureAdmin();
        if ($guard) {
            return $guard;
        }

        $editId = (int) ($this->request->getGet('edit') ?? 0);
        $editType = null;
        if ($editId > 0) {
            $editType = $this->typeCongeModel->find($editId);
        }

        $types = $this->typeCongeModel->orderBy('nom', 'ASC')->findAll();

        return view('admin/types', [
            'types' => $types,
            'editType' => $editType,
        ]);
    }

    public function saveTypeConge()
    {
        $guard = $this->ensureAdmin();
        if ($guard) {
            return $guard;
        }

        $nom = trim((string) $this->request->getPost('nom'));
        $jours = (int) $this->request->getPost('jours_annuels');
        $deductible = (int) $this->request->getPost('deductible');
        $description = trim((string) $this->request->getPost('description'));

        if ($nom === '') {
            return redirect()->back()->withInput()->with('error', 'Le nom du type de conge est requis.');
        }

        $this->typeCongeModel->insert([
            'nom' => $nom,
            'jours_annuels' => $jours,
            'deductible' => $deductible === 1 ? 1 : 0,
            'description' => $description !== '' ? $description : null,
        ]);

        return redirect()->to('/admin/types')->with('success', 'Type de conge cree.');
    }

    public function updateTypeConge(int $id)
    {
        $guard = $this->ensureAdmin();
        if ($guard) {
            return $guard;
        }

        $nom = trim((string) $this->request->getPost('nom'));
        $jours = (int) $this->request->getPost('jours_annuels');
        $deductible = (int) $this->request->getPost('deductible');
        $description = trim((string) $this->request->getPost('description'));

        if ($nom === '') {
            return redirect()->back()->withInput()->with('error', 'Le nom du type de conge est requis.');
        }

        $this->typeCongeModel->update($id, [
            'nom' => $nom,
            'jours_annuels' => $jours,
            'deductible' => $deductible === 1 ? 1 : 0,
            'description' => $description !== '' ? $description : null,
        ]);

        return redirect()->to('/admin/types')->with('success', 'Type de conge mis a jour.');
    }

    public function deleteTypeConge(int $id)
    {
        $guard = $this->ensureAdmin();
        if ($guard) {
            return $guard;
        }

        $used = $this->db->table('conges')->where('type_conge_id', $id)->countAllResults();
        if ($used > 0) {
            return redirect()->to('/admin/types')->with('error', 'Impossible de supprimer un type utilise.');
        }

        $this->typeCongeModel->delete($id);
        return redirect()->to('/admin/types')->with('success', 'Type de conge supprime.');
    }

    public function soldes()
    {
        $guard = $this->ensureAdmin();
        if ($guard) {
            return $guard;
        }

        $annee = (int) ($this->request->getGet('annee') ?? date('Y'));

        $employes = $this->employeModel->orderBy('nom', 'ASC')->findAll();
        $types = $this->typeCongeModel->orderBy('nom', 'ASC')->findAll();

        $soldes = $this->db->table('soldes s')
            ->select('s.*, e.nom AS employe_nom, e.prenom AS employe_prenom, t.nom AS type_conge_nom')
            ->join('employes e', 'e.id = s.employe_id', 'left')
            ->join('types_conge t', 't.id = s.type_conge_id', 'left')
            ->where('s.annee', $annee)
            ->orderBy('e.nom', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/soldes', [
            'annee' => $annee,
            'employes' => $employes,
            'types' => $types,
            'soldes' => $soldes,
        ]);
    }

    public function saveSolde()
    {
        $guard = $this->ensureAdmin();
        if ($guard) {
            return $guard;
        }

        $employeId = (int) $this->request->getPost('employe_id');
        $typeId = (int) $this->request->getPost('type_conge_id');
        $annee = (int) $this->request->getPost('annee');
        $attribues = (int) $this->request->getPost('jours_attribues');
        $pris = (int) $this->request->getPost('jours_pris');

        if ($employeId <= 0 || $typeId <= 0 || $annee <= 0) {
            return redirect()->back()->withInput()->with('error', 'Employe, type et annee sont requis.');
        }

        $existing = $this->soldeModel
            ->where('employe_id', $employeId)
            ->where('type_conge_id', $typeId)
            ->where('annee', $annee)
            ->first();

        $data = [
            'employe_id' => $employeId,
            'type_conge_id' => $typeId,
            'jours_attribues' => $attribues,
            'jours_pris' => $pris,
            'annee' => $annee,
        ];

        if ($existing) {
            $this->soldeModel->update((int) $existing['id'], $data);
        } else {
            $this->soldeModel->insert($data);
        }

        return redirect()->to('/admin/soldes?annee=' . $annee)->with('success', 'Solde mis a jour.');
    }

    public function conges()
    {
        $guard = $this->ensureAdmin();
        if ($guard) {
            return $guard;
        }

        $statut = $this->request->getGet('statut') ?? 'all';

        $builder = $this->db->table('conges c')
            ->select('c.*, e.nom AS employe_nom, e.prenom AS employe_prenom, d.nom AS departement_nom, t.nom AS type_conge_nom')
            ->join('employes e', 'e.id = c.employe_id', 'left')
            ->join('departements d', 'd.id = e.departement_id', 'left')
            ->join('types_conge t', 't.id = c.type_conge_id', 'left')
            ->orderBy('c.date_debut', 'DESC');

        if ($statut !== 'all') {
            $builder->where('c.statut', $statut);
        }

        $demandes = $builder->get()->getResultArray();

        return view('admin/conges', [
            'statut' => $statut,
            'demandes' => $demandes,
        ]);
    }
}
