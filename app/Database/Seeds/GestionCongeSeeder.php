<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class GestionCongeSeeder extends Seeder
{
    public function run()
    {
        $departements = [
            ['nom' => 'Informatique', 'description' => 'Developpement et support technique'],
            ['nom' => 'Ressources Humaines', 'description' => 'Gestion RH et administration du personnel'],
            ['nom' => 'Administration', 'description' => 'Administration generale'],
        ];

        foreach ($departements as $dep) {
            $exists = $this->db->table('departements')->where('nom', $dep['nom'])->countAllResults();
            if ($exists === 0) {
                $this->db->table('departements')->insert($dep);
            }
        }

        $typesConge = [
            ['nom' => 'Conge annuel', 'jours_annuels' => 30, 'deductible' => 1, 'description' => 'Conge paye annuel'],
            ['nom' => 'Conge maladie', 'jours_annuels' => 10, 'deductible' => 0, 'description' => 'Arret maladie'],
            ['nom' => 'Conge exceptionnel', 'jours_annuels' => 5, 'deductible' => 0, 'description' => 'Evenements exceptionnels'],
        ];

        foreach ($typesConge as $type) {
            $exists = $this->db->table('types_conge')->where('nom', $type['nom'])->countAllResults();
            if ($exists === 0) {
                $this->db->table('types_conge')->insert($type);
            }
        }

        $departementsMap = [];
        $resDeps = $this->db->table('departements')->select('id, nom')->get()->getResultArray();
        foreach ($resDeps as $d) {
            $departementsMap[$d['nom']] = (int) $d['id'];
        }

        $employes = [
            [
                'nom' => 'Admin',
                'prenom' => 'Systeme',
                'email' => 'admin@test.local',
                'mot_de_passe' => 'admin123',
                'role' => 'admin',
                'date_embauche' => '2024-01-01',
                'actif' => 1,
                'departement_id' => $departementsMap['Administration'] ?? null,
            ],
            [
                'nom' => 'Rakoto',
                'prenom' => 'Employe',
                'email' => 'employe@test.local',
                'mot_de_passe' => 'employe123',
                'role' => 'employe',
                'date_embauche' => '2024-01-15',
                'actif' => 1,
                'departement_id' => $departementsMap['Informatique'] ?? null,
            ],
            [
                'nom' => 'Rasoanaivo',
                'prenom' => 'RH',
                'email' => 'rh@test.local',
                'mot_de_passe' => 'rh123',
                'role' => 'rh',
                'date_embauche' => '2024-01-10',
                'actif' => 1,
                'departement_id' => $departementsMap['Ressources Humaines'] ?? null,
            ],
        ];

        foreach ($employes as $emp) {
            $exists = $this->db->table('employes')->where('email', $emp['email'])->countAllResults();
            if ($exists === 0) {
                $this->db->table('employes')->insert($emp);
            }
        }

        $typesMap = [];
        $resTypes = $this->db->table('types_conge')->select('id, nom, jours_annuels')->get()->getResultArray();
        foreach ($resTypes as $t) {
            $typesMap[$t['nom']] = [
                'id' => (int) $t['id'],
                'jours_annuels' => (int) $t['jours_annuels'],
            ];
        }

        $annee = (int) date('Y');
        $employesDb = $this->db->table('employes')->select('id, role')->get()->getResultArray();

        foreach ($employesDb as $empDb) {
            foreach ($typesMap as $type) {
                $exists = $this->db->table('soldes')
                    ->where('employe_id', (int) $empDb['id'])
                    ->where('type_conge_id', $type['id'])
                    ->where('annee', $annee)
                    ->countAllResults();

                if ($exists === 0) {
                    $this->db->table('soldes')->insert([
                        'employe_id' => (int) $empDb['id'],
                        'type_conge_id' => $type['id'],
                        'jours_attribues' => $type['jours_annuels'],
                        'jours_pris' => 0,
                        'annee' => $annee,
                    ]);
                }
            }
        }
    }
}
