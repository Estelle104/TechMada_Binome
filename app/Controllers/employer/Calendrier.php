<?php
namespace App\Controllers\employer;

use App\Controllers\BaseController;
use App\Models\CongeModel;

class Calendrier extends BaseController
{
    public function index()
    {
        return view('employer/calendrier');
    }

    public function events() {
        $model = new CongeModel();
        $conges = $model->where('statut', 'approuvee')->findAll();
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