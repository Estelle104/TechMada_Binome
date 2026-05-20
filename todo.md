# Répartition des tâches — Projet Gestion de Congés RH

# ANDRY — Gestion Employé + Authentification

Responsable de toute la partie utilisateur/employé.

---

# 1. Initialisation authentification

## Tâches

* [ok] Configuration sessions CI4
* [ok] Login
* [ok] Logout
* [ok] Middleware / filtre authentification
* [ok] Restriction par rôle
* [ok] Gestion mot de passe hashé

---

# 2. Module Profil Employé

## Tâches

* [ok] Affichage profil
* [ok] Modification profil
* [ok] Changement mot de passe
* [ok] Validation formulaires

---

# 3. Dashboard Employé

## Tâches

* [ok] Nombre congés restants
* [ok] Nombre demandes en attente
* [ok] Dernières demandes
* [ok] Interface dashboard

---

# 4. Module Demande de Congé

## Tâches

* [ok] Formulaire création demande
* [ok] Validation dates
* [ok] Calcul nombre jours
* [ok] Vérification chevauchement
* [ok] Vérification solde suffisant
* [ok] Enregistrement demande

---

# 5. Historique des demandes

## Tâches

* [ok] Liste des demandes
* [ok] Filtrage par statut
* [ok] Voir détail demande
* [ok] Affichage commentaires RH

---

# 6. Annulation demande

## Tâches

* [ok] Annulation demande en attente
* [ok] Vérification statut
* [ok] Mise à jour statut

---

# 7. Front-end Employé

## Tâches

* [ok] Layout employé
* [ok] Navbar
* [ok] Sidebar
* [ok] Responsive design
* [ok] Messages erreurs/succès

---

# Livrables Personne 1

## Controllers

```text
AuthController
Employe/DashboardController
Employe/CongeController
Employe/ProfilController
```

---

## Views

```text
views/auth/
views/employe/
```

---

## Models

```text
EmployeModel
CongeModel
```

---

# ESTELLE — Administration + RH

Responsable de toute la gestion administrative et validation RH.

---

# 1. Base de données + migrations

## Tâches

* [ok] Création migrations
* [ok] Relations tables
* [ok] Seeders
* [ok] Contraintes SQL
* [ok] Initialisation SQLite

---

# 2. Gestion Départements

## Tâches

* [ok] CRUD départements
* [ok] Validation
* [ok] Suppression sécurisée

---

# 3. Gestion Types de Congés

## Tâches

* [ok] CRUD types congés
* [ok] Gestion jours annuels
* [ok] Gestion déductible

---

# 4. Gestion Employés (Admin)

## Tâches

* [ok] Ajouter employé
* [ok] Modifier employé
* [ok] Désactiver employé
* [ok] Réinitialiser mot de passe
* [ok] Attribution rôles

---

# 5. Gestion Soldes

## Tâches

* [ok] Attribution soldes
* [ok] Modification soldes
* [ok] Réinitialisation annuelle
* [ok] Consultation soldes

---

# 6. Module RH — Validation des congés

## Tâches

* [ok] Liste toutes demandes
* [ok] Filtrage
* [ok] Validation demande
* [ok] Refus demande
* [ok] Commentaire RH
* [ok] Mise à jour soldes après validation

---

# 7. Dashboard RH/Admin

## Tâches

* [ok] Statistiques générales
* [ok] Nombre employés
* [ok] Nombre demandes
* [ok] Congés approuvés/refusés
* [ok] Interface dashboard

---

# 8. Front-end Admin/RH

## Tâches

* [ok] Layout admin
* [ok] Tables CRUD
* [ok] Pagination
* [ok] Messages succès/erreurs

---

# Livrables Personne 2

## Controllers

```text
Admin/EmployeController
Admin/DepartementController
Admin/TypeCongeController
Admin/SoldeController
RH/CongeController
```

---

## Models

```text
DepartementModel
TypeCongeModel
SoldeModel
```

---

# 20-05-26
## Employe 
- Calendrier interactive : Estelle
- Historique et statistiques : Estelle

## Admin
- Graphique sur le nombre de conge par mois : Andry
- Graphique sur les jours : Andry