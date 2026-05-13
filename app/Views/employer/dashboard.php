<!DOCTYPE html>
<html lang="fr">
<?= view('partials/template_head', ['title' => 'Dashboard Employe - TechMada RH']) ?>
<body>
<?php
$employe = $employe ?? [];
$counts = $counts ?? ['en_attente' => 0, 'approuvee' => 0, 'refusee' => 0, 'annulee' => 0];
$soldes = $soldes ?? [];
$annual = $annual ?? ['attribues' => 0, 'restants' => 0];
$recentDemandes = $recentDemandes ?? [];
$prenom = (string) ($employe['prenom'] ?? '');
$nom = (string) ($employe['nom'] ?? '');
$departementNom = (string) ($employe['departement_nom'] ?? '');
$initials = trim(substr($prenom, 0, 1) . substr($nom, 0, 1));
$initials = $initials !== '' ? strtoupper($initials) : 'EM';
?>
<div class="app-wrap">
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div>
    </div>
    <div class="sidebar-section">Menu</div>
    <ul class="sidebar-nav">
      <li><a href="/employer" class="active"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
      <li><a href="/employer/conges/nouvelle"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
      <li>
        <a href="/employer/conges/mes">
          <i class="bi bi-calendar3"></i> Mes demandes
          <span class="nav-badge alert"><?= esc((string) $counts['en_attente']) ?></span>
        </a>
      </li>
      <li><a href="/employer/profil"><i class="bi bi-person"></i> Mon profil</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar av-green"><?= esc($initials) ?></div>
        <div>
          <div class="user-name"><?= esc(trim($prenom . ' ' . $nom)) ?></div>
          <div class="user-role">Employé · <?= esc($departementNom !== '' ? $departementNom : 'Sans département') ?></div>
        </div>
        <a href="/logout" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem" title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Tableau de bord</div>
        <div class="topbar-breadcrumb">Accueil</div>
      </div>
      <div class="topbar-actions">
        <a href="/employer/conges/nouvelle" class="btn-forest" style="padding:7px 14px;font-size:.82rem">
          <i class="bi bi-plus-lg"></i> Nouvelle demande
        </a>
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

      <div class="metrics">
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div></div>
          <div class="metric-val"><?= esc((string) $counts['en_attente']) ?></div>
          <div class="metric-label">En attente</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-check-circle"></i></div></div>
          <div class="metric-val"><?= esc((string) $counts['approuvee']) ?></div>
          <div class="metric-label">Approuvées</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-forest"><i class="bi bi-calendar-check"></i></div></div>
          <div class="metric-val"><?= esc((string) $annual['restants']) ?></div>
          <div class="metric-label">Jours restants</div>
          <div class="metric-sub">sur <?= esc((string) $annual['attribues']) ?> cette année</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-red"><i class="bi bi-x-circle"></i></div></div>
          <div class="metric-val"><?= esc((string) $counts['refusee']) ?></div>
          <div class="metric-label">Refusée</div>
        </div>
      </div>

      <div class="data-card">
        <div class="data-card-head"><h3>Mes soldes de congés — <?= esc((string) $annee) ?></h3></div>
        <div style="padding:1rem 1.25rem;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem">
          <?php if (empty($soldes)): ?>
            <div class="empty"><i class="bi bi-inbox"></i><p>Aucun solde disponible.</p></div>
          <?php endif; ?>
          <?php foreach ($soldes as $solde): ?>
            <?php
              $attribues = (int) $solde['attribues'];
              $restants = (int) $solde['restants'];
              $pris = (int) $solde['pris'];
              $percent = $attribues > 0 ? (int) round(($restants / $attribues) * 100) : 0;
              $fillClass = $percent <= 20 ? 'danger' : ($percent <= 40 ? 'warn' : '');
            ?>
            <div class="solde-card" style="margin:0">
              <div class="solde-header">
                <span class="solde-type"><?= esc((string) $solde['type_nom']) ?></span>
                <span class="solde-nums"><strong><?= esc((string) $restants) ?></strong> / <?= esc((string) $attribues) ?> j</span>
              </div>
              <div class="solde-bar"><div class="solde-fill <?= esc($fillClass) ?>" style="width:<?= esc((string) $percent) ?>%"></div></div>
              <div class="solde-label"><?= esc((string) $restants) ?> jours restants · <?= esc((string) $pris) ?> pris</div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="data-card">
        <div class="data-card-head">
          <h3>Mes dernières demandes</h3>
          <a href="/employer/conges/mes" style="font-size:.8rem;color:var(--forest);text-decoration:none">Voir tout →</a>
        </div>
        <table class="tbl">
          <thead>
            <tr><th>Type</th><th>Du</th><th>Au</th><th>Durée</th><th>Statut</th><th>Action</th></tr>
          </thead>
          <tbody>
          <?php if (empty($recentDemandes)): ?>
            <tr><td colspan="6" class="td-muted">Aucune demande récente.</td></tr>
          <?php endif; ?>
          <?php foreach ($recentDemandes as $demande): ?>
            <?php
              $statut = (string) $demande['statut'];
              $statusClass = $statut === 'approuvee' ? 's-approuvee' : ($statut === 'refusee' ? 's-refusee' : ($statut === 'annulee' ? 's-annulee' : 's-attente'));
              $typeNom = (string) ($demande['type_conge_nom'] ?? '');
              $typeClass = str_contains(strtolower($typeNom), 'annuel') ? 't-annuel' : (str_contains(strtolower($typeNom), 'maladie') ? 't-maladie' : (str_contains(strtolower($typeNom), 'exception') || str_contains(strtolower($typeNom), 'special') ? 't-special' : 't-sans-solde'));
            ?>
            <tr>
              <td><span class="type-badge <?= esc($typeClass) ?>"><?= esc($typeNom) ?></span></td>
              <td class="td-muted"><?= esc(date('d/m/Y', strtotime((string) $demande['date_debut']))) ?></td>
              <td class="td-muted"><?= esc(date('d/m/Y', strtotime((string) $demande['date_fin']))) ?></td>
              <td class="td-mono"><?= esc((string) $demande['nb_jours']) ?> j</td>
              <td><span class="statut <?= esc($statusClass) ?>"><?= esc($statut) ?></span></td>
              <td>
                <?php if ($statut === 'en_attente'): ?>
                  <form method="post" action="/employer/conges/annuler/<?= esc((string) $demande['id']) ?>" style="margin:0">
                    <?= csrf_field() ?>
                    <button class="btn-sm btn-cancel" type="submit"><i class="bi bi-x"></i> Annuler</button>
                  </form>
                <?php else: ?>
                  <span class="td-muted" style="font-size:.75rem">—</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="footer-app"><i class="bi bi-c-circle"></i> <?= esc((string) date('Y')) ?> <span>TechMada RH</span> — Projet CodeIgniter 4</div>
  </div>
</div>
</body>
</html>