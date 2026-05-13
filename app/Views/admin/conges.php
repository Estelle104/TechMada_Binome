<!DOCTYPE html>
<html lang="fr">
<?= view('partials/template_head', ['title' => 'Historique conges - TechMada RH']) ?>
<body>
<?php
$statut = $statut ?? 'all';
$demandes = $demandes ?? [];
?>
<div class="app-wrap">
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon" style="background:var(--ink);border:1px solid rgba(255,255,255,.15)"><i class="bi bi-shield-check" style="color:var(--leaf)"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Administration</span></div>
    </div>
    <div class="sidebar-section">Gestion</div>
    <ul class="sidebar-nav">
      <li><a href="/admin"><i class="bi bi-speedometer2"></i> Vue d'ensemble</a></li>
      <li><a href="/admin/conges" class="active"><i class="bi bi-inbox"></i> Toutes les demandes</a></li>
      <li><a href="/admin/employes"><i class="bi bi-people"></i> Employes</a></li>
      <li><a href="/admin/departements"><i class="bi bi-building"></i> Departements</a></li>
      <li><a href="/admin/types"><i class="bi bi-tags"></i> Types de conge</a></li>
      <li><a href="/admin/soldes"><i class="bi bi-sliders"></i> Soldes annuels</a></li>
    </ul>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Historique des demandes</div>
        <div class="topbar-breadcrumb"><a href="/admin">Admin</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Conges</div>
      </div>
    </div>

    <div class="content">
      <div class="data-card">
        <div class="data-card-head">
          <h3>Toutes les demandes</h3>
          <form method="get" action="/admin/conges">
            <select class="f-select" name="statut" style="font-size:.8rem;padding:6px 10px;width:auto" onchange="this.form.submit()">
              <option value="all" <?= $statut === 'all' ? 'selected' : '' ?>>Tous les statuts</option>
              <option value="en_attente" <?= $statut === 'en_attente' ? 'selected' : '' ?>>En attente</option>
              <option value="approuvee" <?= $statut === 'approuvee' ? 'selected' : '' ?>>Approuvee</option>
              <option value="refusee" <?= $statut === 'refusee' ? 'selected' : '' ?>>Refusee</option>
              <option value="annulee" <?= $statut === 'annulee' ? 'selected' : '' ?>>Annulee</option>
            </select>
          </form>
        </div>
        <table class="tbl">
          <thead>
            <tr><th>Employe</th><th>Departement</th><th>Type</th><th>Periode</th><th>Duree</th><th>Statut</th><th>Commentaire RH</th></tr>
          </thead>
          <tbody>
          <?php if (empty($demandes)): ?>
            <tr><td colspan="7" class="td-muted">Aucune demande.</td></tr>
          <?php endif; ?>
          <?php foreach ($demandes as $d): ?>
            <?php
              $stat = (string) $d['statut'];
              $statusClass = $stat === 'approuvee' ? 's-approuvee' : ($stat === 'refusee' ? 's-refusee' : ($stat === 'annulee' ? 's-annulee' : 's-attente'));
            ?>
            <tr>
              <td><?= esc((string) (($d['employe_prenom'] ?? '') . ' ' . ($d['employe_nom'] ?? ''))) ?></td>
              <td class="td-muted"><?= esc((string) ($d['departement_nom'] ?? 'Sans departement')) ?></td>
              <td><?= esc((string) ($d['type_conge_nom'] ?? '')) ?></td>
              <td class="td-muted"><?= esc((string) ($d['date_debut'] ?? '')) ?> -> <?= esc((string) ($d['date_fin'] ?? '')) ?></td>
              <td class="td-mono"><?= esc((string) ($d['nb_jours'] ?? 0)) ?> j</td>
              <td><span class="statut <?= esc($statusClass) ?>"><?= esc($stat) ?></span></td>
              <td class="td-muted" style="font-size:.78rem"><?= esc((string) ($d['commentaire_rh'] ?? '—')) ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="footer-app"><i class="bi bi-c-circle"></i> <?= esc((string) date('Y')) ?> <span>TechMada RH</span></div>
  </div>
</div>
</body>
</html>
