# Répartition des tâches — Projet Gestion de Congés RH

# ANDRY — Gestion Employé + Authentification

Responsable de toute la partie utilisateur/employé.

---

# 1. Initialisation authentification

## Tâches

* [ ] Configuration sessions CI4
* [ ] Login
* [ ] Logout
* [ ] Middleware / filtre authentification
* [ ] Restriction par rôle
* [ ] Gestion mot de passe hashé

---

# 2. Module Profil Employé

## Tâches

* [ ] Affichage profil
* [ ] Modification profil
* [ ] Changement mot de passe
* [ ] Validation formulaires

---

# 3. Dashboard Employé

## Tâches

* [ ] Nombre congés restants
* [ ] Nombre demandes en attente
* [ ] Dernières demandes
* [ ] Interface dashboard

---

# 4. Module Demande de Congé

## Tâches

* [ ] Formulaire création demande
* [ ] Validation dates
* [ ] Calcul nombre jours
* [ ] Vérification chevauchement
* [ ] Vérification solde suffisant
* [ ] Enregistrement demande

---

# 5. Historique des demandes

## Tâches

* [ ] Liste des demandes
* [ ] Filtrage par statut
* [ ] Voir détail demande
* [ ] Affichage commentaires RH

---

# 6. Annulation demande

## Tâches

* [ ] Annulation demande en attente
* [ ] Vérification statut
* [ ] Mise à jour statut

---

# 7. Front-end Employé

## Tâches

* [ ] Layout employé
* [ ] Navbar
* [ ] Sidebar
* [ ] Responsive design
* [ ] Messages erreurs/succès

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

* [ ] Création migrations
* [ ] Relations tables
* [ ] Seeders
* [ ] Contraintes SQL
* [ ] Initialisation SQLite

---

# 2. Gestion Départements

## Tâches

* [ ] CRUD départements
* [ ] Validation
* [ ] Suppression sécurisée

---

# 3. Gestion Types de Congés

## Tâches

* [ ] CRUD types congés
* [ ] Gestion jours annuels
* [ ] Gestion déductible

---

# 4. Gestion Employés (Admin)

## Tâches

* [ ] Ajouter employé
* [ ] Modifier employé
* [ ] Désactiver employé
* [ ] Réinitialiser mot de passe
* [ ] Attribution rôles

---

# 5. Gestion Soldes

## Tâches

* [ ] Attribution soldes
* [ ] Modification soldes
* [ ] Réinitialisation annuelle
* [ ] Consultation soldes

---

# 6. Module RH — Validation des congés

## Tâches

* [ ] Liste toutes demandes
* [ ] Filtrage
* [ ] Validation demande
* [ ] Refus demande
* [ ] Commentaire RH
* [ ] Mise à jour soldes après validation

---

# 7. Dashboard RH/Admin

## Tâches

* [ ] Statistiques générales
* [ ] Nombre employés
* [ ] Nombre demandes
* [ ] Congés approuvés/refusés
* [ ] Interface dashboard

---

# 8. Front-end Admin/RH

## Tâches

* [ ] Layout admin
* [ ] Tables CRUD
* [ ] Pagination
* [ ] Messages succès/erreurs

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
