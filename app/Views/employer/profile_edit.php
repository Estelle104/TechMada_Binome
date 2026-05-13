<!DOCTYPE html>
<html lang="fr">
<?= view('partials/template_head', ['title' => 'Modifier mon profil - TechMada RH']) ?>
<body>
<?php
$employe = $employe ?? [];
$oldInput = session()->getFlashdata('_ci_old_input') ?? [];
$nom = (string) ($oldInput['nom'] ?? ($employe['nom'] ?? ''));
$prenom = (string) ($oldInput['prenom'] ?? ($employe['prenom'] ?? ''));
$email = (string) ($oldInput['email'] ?? ($employe['email'] ?? ''));
$flashError = session()->getFlashdata('error');
$flashSuccess = session()->getFlashdata('success');
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
        <div class="topbar-title">Modifier mon profil</div>
        <div class="topbar-breadcrumb"><a href="/employer">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Profil</div>
      </div>
    </div>

    <div class="content">
      <?php if ($flashSuccess): ?>
        <div class="flash flash-success">
          <i class="bi bi-check-circle-fill"></i>
          <?= esc((string) $flashSuccess) ?>
        </div>
      <?php endif; ?>
      <?php if ($flashError): ?>
        <div class="flash flash-error">
          <i class="bi bi-exclamation-circle-fill"></i>
          <?= esc((string) $flashError) ?>
        </div>
      <?php endif; ?>

      <div class="form-section">
        <h3>Informations personnelles</h3>
        <form method="post" action="/employer/profil/modifier">
          <?= csrf_field() ?>
          <div class="form-grid-2" style="margin-bottom:1rem">
            <div class="f-group">
              <label class="f-label">Nom</label>
              <input type="text" name="nom" class="f-input" value="<?= esc($nom) ?>" required />
            </div>
            <div class="f-group">
              <label class="f-label">Prenom</label>
              <input type="text" name="prenom" class="f-input" value="<?= esc($prenom) ?>" required />
            </div>
            <div class="f-group">
              <label class="f-label">Email</label>
              <input type="email" name="email" class="f-input" value="<?= esc($email) ?>" required />
            </div>
          </div>

          <div class="form-section" style="padding:1.25rem;margin:0 0 1rem">
            <h3>Changer le mot de passe</h3>
            <div class="form-grid-2">
              <div class="f-group">
                <label class="f-label">Nouveau mot de passe</label>
                <input type="password" name="mot_de_passe" class="f-input" />
              </div>
              <div class="f-group">
                <label class="f-label">Confirmer le mot de passe</label>
                <input type="password" name="mot_de_passe_confirm" class="f-input" />
              </div>
            </div>
            <div class="f-hint">Laissez vide si vous ne souhaitez pas changer le mot de passe.</div>
          </div>

          <div class="form-actions">
            <button class="btn-forest" type="submit"><i class="bi bi-check2"></i> Enregistrer</button>
            <a href="/employer/profil" class="btn-secondary"><i class="bi bi-x"></i> Annuler</a>
          </div>
        </form>
      </div>
    </div>

    <div class="footer-app"><i class="bi bi-c-circle"></i> <?= esc((string) date('Y')) ?> <span>TechMada RH</span></div>
  </div>
</div>
</body>
</html>
