<?php
namespace App\Controllers\employer;

use App\Controllers\BaseController;
use App\Models\CongeModel;
use App\Models\EmployeModel;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\HTTP\RedirectResponse;

class Calendrier extends BaseController
{
    private BaseConnection $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function index()
    {
        $user = session()->get('user');
        if (!$user || ($user['role'] ?? '') !== 'employe') {
            return redirect()->to('/')->with('error', 'Acces refuse');
        }

        $employeId = (int) $user['id'];
        $model = new CongeModel();

        // Récupérer les données employé
        $employe = $this->db->table('employes e')
            ->select('e.nom, e.prenom, e.email, e.role, e.date_embauche, d.nom AS departement_nom')
            ->join('departements d', 'd.id = e.departement_id', 'left')
            ->where('e.id', $employeId)
            ->get()
            ->getRowArray();

        // Statistiques
        $counts = [
            'en_attente' => $model->where('employe_id', $employeId)->where('statut', 'en_attente')->countAllResults(),
            'approuvee' => $model->where('employe_id', $employeId)->where('statut', 'approuvee')->countAllResults(),
            'refusee' => $model->where('employe_id', $employeId)->where('statut', 'refusee')->countAllResults(),
            'annulee' => $model->where('employe_id', $employeId)->where('statut', 'annulee')->countAllResults(),
        ];

        // Historique (derniers 5 congés)
        $historique = $this->db->table('conges c')
            ->select('c.id, c.date_debut, c.date_fin, c.nb_jours, c.motif, c.statut, c.type_conge_id')
            ->where('c.employe_id', $employeId)
            ->orderBy('c.date_debut', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        // Ajouter le type de congé s'il existe
        foreach ($historique as &$h) {
            $h['type_conge_nom'] = 'Congé'; // Valeur par défaut
        }

        return view('employer/calendrier', [
            'employe' => $employe,
            'counts' => $counts,
            'historique' => $historique
        ]);
    }

    public function events() {
        $user = session()->get('user');
        $employeId = (int) $user['id'];
        
        $model = new CongeModel();
        $conges = $model->where('employe_id', $employeId)->where('statut', 'approuvee')->findAll();
        $events = [];

        foreach($conges as $c) {
            $debut = $c['date_debut'];
            $fin = $c['date_fin'];

            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $debut) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fin)) {
                $fin = date('Y-m-d', strtotime($fin . ' +1 day'));
                $allDay = true;
            } else {
                $allDay = false;
            }

            $events[] = [
                'id' => $c['id'],
                'title' => $c['motif'] ?? 'Congé',
                'start' => $debut,
                'end' => $fin,
                'allDay' => $allDay,
                'backgroundColor' => '#2d5a3d',
                'textColor' => '#ffffff'
            ];
        }
        return $this->response->setJSON($events);
    }

}