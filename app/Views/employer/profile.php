<!DOCTYPE html>
<html lang="fr">
<?= view('partials/template_head', ['title' => 'Mon profil - TechMada RH']) ?>
<body>
<?php
$employe = $employe ?? [];
$prenom = (string) ($employe['prenom'] ?? '');
$nom = (string) ($employe['nom'] ?? '');
$email = (string) ($employe['email'] ?? '');
$departementNom = (string) ($employe['departement_nom'] ?? '');
$departementDesc = (string) ($employe['departement_description'] ?? '');
$role = (string) ($employe['role'] ?? '');
$dateEmbauche = (string) ($employe['date_embauche'] ?? '');
$initials = trim(substr($prenom, 0, 1) . substr($nom, 0, 1));
$initials = $initials !== '' ? strtoupper($initials) : 'EM';
?>
<div class="app-wrap">
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div>
    </div>
    <ul class="sidebar-nav" style="margin-top:1rem">
      <li><a href="/employer"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
      <li><a href="/employer/conges/nouvelle"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
      <li><a href="/employer/conges/mes"><i class="bi bi-calendar3"></i> Mes demandes</a></li>
      <li><a href="/employer/profil" class="active"><i class="bi bi-person"></i> Mon profil</a></li>
    </ul>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Mon profil</div>
        <div class="topbar-breadcrumb"><a href="/employer">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Profil</div>
      </div>
    </div>

    <div class="content">
      <?php if (session()->getFlashdata('success')): ?>
        <div class="flash flash-success">
          <i class="bi bi-check-circle-fill"></i>
          <?= esc((string) session()->getFlashdata('success')) ?>
        </div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('error')): ?>
        <div class="flash flash-error">
          <i class="bi bi-exclamation-circle-fill"></i>
          <?= esc((string) session()->getFlashdata('error')) ?>
        </div>
      <?php endif; ?>

      <div class="data-card">
        <div class="data-card-head"><h3>Informations personnelles</h3></div>
        <div style="padding:1.25rem">
          <div class="profile-row" style="margin-bottom:1rem">
            <div class="avatar av-green" style="width:44px;height:44px;font-size:.8rem"><?= esc($initials) ?></div>
            <div class="profile-info">
              <div class="pname"><?= esc(trim($prenom . ' ' . $nom)) ?></div>
              <div class="pdept"><?= esc($departementNom !== '' ? $departementNom : 'Sans département') ?></div>
            </div>
          </div>
          <div class="inline-stats">
            <div class="inline-stat"><i class="bi bi-envelope"></i> <strong><?= esc($email) ?></strong></div>
            <div class="inline-stat"><i class="bi bi-person-badge"></i> <strong><?= esc($role) ?></strong></div>
            <div class="inline-stat"><i class="bi bi-calendar-event"></i> <strong><?= esc($dateEmbauche) ?></strong></div>
          </div>
          <div style="margin-top:1rem">
            <a href="/employer/profil/modifier" class="btn-secondary"><i class="bi bi-pencil"></i> Modifier mon profil</a>
          </div>
        </div>
      </div>

      <div class="data-card">
        <div class="data-card-head"><h3>Département</h3></div>
        <div style="padding:1.25rem">
          <p style="margin:0;font-weight:500"><?= esc($departementNom !== '' ? $departementNom : 'Non renseigné') ?></p>
          <p style="margin:.25rem 0 0;color:var(--muted)"><?= esc($departementDesc !== '' ? $departementDesc : 'Aucune description disponible.') ?></p>
        </div>
      </div>
    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> <?= esc((string) date('Y')) ?> <span>TechMada RH</span></div>
  </div>
</div>
</body>
</html>
