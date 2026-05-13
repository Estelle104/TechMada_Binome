# TechMada RH - Gestion des Conges (CI4)

Application CodeIgniter 4 pour gerer les demandes de conges avec 3 roles:
- `employe`
- `rh` (responsable RH)
- `admin`

## Mise en place rapide

Prerequis:
- PHP 8.2+
- Extension `sqlite3` active
- Composer

Commandes d installation:

```bash
composer install
php spark migrate
php spark db:seed DatabaseSeeder
php spark serve
```

L application sera disponible sur `http://localhost:8080`.

## Comptes de test

Apres `php spark db:seed DatabaseSeeder`, les comptes suivants existent:

- `admin@test.local` / `admin123` (role `admin`)
- `rh@test.local` / `rh123` (role `rh`)
- `employe@test.local` / `employe123` (role `employe`)

## Workflow DB exige

Conformement aux consignes:
- schema gere par migration CI4
- donnees de test gerees par seeder CI4

Commande standard:

```bash
php spark migrate && php spark db:seed DatabaseSeeder
```

## Fonctionnalites par role (consignes)

### Employe
- Connexion / deconnexion
- Soumettre une demande de conge (type, dates, motif)
- Consulter ses demandes et leurs statuts
- Voir son solde restant par type
- Annuler une demande en attente
- Modifier son profil

### Responsable RH
- Voir toutes les demandes en attente
- Approuver ou refuser une demande (avec commentaire)
- Mise a jour automatique du solde a l approbation
- Filtrer les demandes par departement ou statut
- Voir le solde de chaque employe

### Admin
- CRUD employes
- CRUD departements
- CRUD types de conge
- Tableau de bord des absences
- Ajuster les soldes annuels
- Voir l historique complet

## Structure des donnees

Tables principales:
- `departements`
- `employes`
- `types_conge`
- `soldes`
- `conges`

Reference schema: `table.sql`

## Dossiers utiles

- `app/Database/Migrations` : schema versionne
- `app/Database/Seeds` : donnees de test
- `app/Controllers/respRH` : logique Responsable RH
- `app/Views/respRH` : interface Responsable RH
