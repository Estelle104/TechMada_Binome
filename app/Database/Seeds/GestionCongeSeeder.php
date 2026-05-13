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
            $exists = $this->db->table('employes')->where('email', $emp['email'])->get()->getRowArray();
            $hashed = password_hash($emp['mot_de_passe'], PASSWORD_DEFAULT);
            if (!$exists) {
                $emp['mot_de_passe'] = $hashed;
                $this->db->table('employes')->insert($emp);
            } else {
                // ensure password stored is hashed (update test accounts)
                $this->db->table('employes')->where('email', $emp['email'])->update(['mot_de_passe' => $hashed]);
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

        $employesMap = [];
        foreach ($employesDb as $e) {
            $employesMap[$e['role']] = (int) $e['id'];
        }

        $conges = [
            [
                'employe_id' => $employesMap['employe'] ?? null,
                'type_conge_id' => $typesMap['Conge annuel']['id'] ?? null,
                'date_debut' => date('Y') . '-06-03',
                'date_fin' => date('Y') . '-06-05',
                'nb_jours' => 3,
                'motif' => 'Conges personnels',
                'statut' => 'en_attente',
                'commentaire_rh' => null,
                'traite_par' => null,
                'date_traitement' => null,
            ],
            [
                'employe_id' => $employesMap['employe'] ?? null,
                'type_conge_id' => $typesMap['Conge maladie']['id'] ?? null,
                'date_debut' => date('Y') . '-02-12',
                'date_fin' => date('Y') . '-02-13',
                'nb_jours' => 2,
                'motif' => 'Consultation medicale',
                'statut' => 'approuvee',
                'commentaire_rh' => 'Justificatif recu',
                'traite_par' => $employesMap['rh'] ?? null,
                'date_traitement' => date('Y') . '-02-11 09:30:00',
            ],
        ];

        foreach ($conges as $conge) {
            if (empty($conge['employe_id']) || empty($conge['type_conge_id'])) {
                continue;
            }

            $exists = $this->db->table('conges')
                ->where('employe_id', (int) $conge['employe_id'])
                ->where('type_conge_id', (int) $conge['type_conge_id'])
                ->where('date_debut', $conge['date_debut'])
                ->where('date_fin', $conge['date_fin'])
                ->countAllResults();

            if ($exists === 0) {
                $this->db->table('conges')->insert($conge);
            }
        }
    }
}
