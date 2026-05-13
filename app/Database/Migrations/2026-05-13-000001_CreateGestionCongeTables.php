<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGestionCongeTables extends Migration
{
    public function up()
    {
        $this->db->query('PRAGMA foreign_keys = ON');

        $this->db->query(
            "CREATE TABLE IF NOT EXISTS departements (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nom TEXT NOT NULL UNIQUE,
                description TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )"
        );

        $this->db->query(
            "CREATE TABLE IF NOT EXISTS employes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nom TEXT NOT NULL,
                prenom TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                mot_de_passe TEXT NOT NULL,
                role TEXT NOT NULL CHECK(role IN ('employe', 'rh', 'admin')),
                date_embauche DATE NOT NULL,
                actif INTEGER NOT NULL DEFAULT 1 CHECK(actif IN (0,1)),
                departement_id INTEGER,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (departement_id)
                    REFERENCES departements(id)
                    ON DELETE SET NULL
            )"
        );

        $this->db->query(
            "CREATE TABLE IF NOT EXISTS types_conge (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nom TEXT NOT NULL UNIQUE,
                jours_annuels INTEGER NOT NULL DEFAULT 0,
                deductible INTEGER NOT NULL DEFAULT 1 CHECK(deductible IN (0,1)),
                description TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )"
        );

        $this->db->query(
            "CREATE TABLE IF NOT EXISTS soldes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                employe_id INTEGER NOT NULL,
                type_conge_id INTEGER NOT NULL,
                jours_attribues INTEGER NOT NULL DEFAULT 0,
                jours_pris INTEGER NOT NULL DEFAULT 0,
                annee INTEGER NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (employe_id)
                    REFERENCES employes(id)
                    ON DELETE CASCADE,
                FOREIGN KEY (type_conge_id)
                    REFERENCES types_conge(id)
                    ON DELETE CASCADE,
                UNIQUE(employe_id, type_conge_id, annee)
            )"
        );

        $this->db->query(
            "CREATE TABLE IF NOT EXISTS conges (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                employe_id INTEGER NOT NULL,
                type_conge_id INTEGER NOT NULL,
                date_debut DATE NOT NULL,
                date_fin DATE NOT NULL,
                nb_jours INTEGER NOT NULL,
                motif TEXT,
                statut TEXT NOT NULL DEFAULT 'en_attente'
                    CHECK(statut IN ('en_attente', 'approuvee', 'refusee', 'annulee')),
                commentaire_rh TEXT,
                traite_par INTEGER,
                date_traitement DATETIME,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (employe_id)
                    REFERENCES employes(id)
                    ON DELETE CASCADE,
                FOREIGN KEY (type_conge_id)
                    REFERENCES types_conge(id)
                    ON DELETE CASCADE,
                FOREIGN KEY (traite_par)
                    REFERENCES employes(id)
                    ON DELETE SET NULL
            )"
        );

        $this->db->query('CREATE INDEX IF NOT EXISTS idx_employes_departement ON employes(departement_id)');
        $this->db->query('CREATE INDEX IF NOT EXISTS idx_soldes_employe ON soldes(employe_id)');
        $this->db->query('CREATE INDEX IF NOT EXISTS idx_conges_employe ON conges(employe_id)');
        $this->db->query('CREATE INDEX IF NOT EXISTS idx_conges_statut ON conges(statut)');
        $this->db->query('CREATE INDEX IF NOT EXISTS idx_conges_dates ON conges(date_debut, date_fin)');
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS conges');
        $this->db->query('DROP TABLE IF EXISTS soldes');
        $this->db->query('DROP TABLE IF EXISTS types_conge');
        $this->db->query('DROP TABLE IF EXISTS employes');
        $this->db->query('DROP TABLE IF EXISTS departements');
    }
}
