<!DOCTYPE html>
<html lang="fr">
<?= view('partials/template_head', ['title' => 'Administration - TechMada RH']) ?>
<body>
<?php
$metrics = $metrics ?? [
    'employes_actifs' => 0,
    'demandes_en_attente' => 0,
    'approuvees_mois' => 0,
    'departements' => 0,
    'absents' => 0,
];
$recentDemandes = $recentDemandes ?? [];
$absentsList = $absentsList ?? [];
?>
<div class="app-wrap">
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon" style="background:var(--ink);border:1px solid rgba(255,255,255,.15)"><i class="bi bi-shield-check" style="color:var(--leaf)"></i></div>
      <div class="sidebar-brand-name">TechMada RH
        <span>Administration</span>
      </div>
    </div>
    <div class="sidebar-section">Gestion</div>
    <ul class="sidebar-nav">
      <li><a href="/admin" class="active"><i class="bi bi-speedometer2"></i> Vue d'ensemble</a></li>
      <li><a href="/admin/conges"><i class="bi bi-inbox"></i> Toutes les demandes <span class="nav-badge alert"><?= esc((string) $metrics['demandes_en_attente']) ?></span></a></li>
      <li><a href="/admin/employes"><i class="bi bi-people"></i> Employés</a></li>
      <li><a href="/admin/departements"><i class="bi bi-building"></i> Départements</a></li>
      <li><a href="/admin/types"><i class="bi bi-tags"></i> Types de congé</a></li>
      <li><a href="/admin/soldes"><i class="bi bi-sliders"></i> Soldes annuels</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar" style="background:#5a2d82;width:32px;height:32px;font-size:.7rem">AD</div>
        <div><div class="user-name">Administrateur</div><div class="user-role">Admin système</div></div>
        <a href="/logout" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Vue d'ensemble</div>
        <div class="topbar-breadcrumb">Administration</div>
      </div>
      <div class="topbar-actions">
        <a href="/admin" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-person-plus"></i> Ajouter un employé</a>
      </div>
    </div>

    <div class="content">
      <div class="metrics">
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-forest"><i class="bi bi-people"></i></div></div>
          <div class="metric-val"><?= esc((string) $metrics['employes_actifs']) ?></div>
          <div class="metric-label">Employés actifs</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div></div>
          <div class="metric-val"><?= esc((string) $metrics['demandes_en_attente']) ?></div>
          <div class="metric-label">Demandes en attente</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-calendar-check"></i></div></div>
          <div class="metric-val"><?= esc((string) $metrics['approuvees_mois']) ?></div>
          <div class="metric-label">Approuvées ce mois</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-blue"><i class="bi bi-building"></i></div></div>
          <div class="metric-val"><?= esc((string) $metrics['departements']) ?></div>
          <div class="metric-label">Départements</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-red"><i class="bi bi-person-slash"></i></div></div>
          <div class="metric-val"><?= esc((string) $metrics['absents']) ?></div>
          <div class="metric-label">Absences du mois</div>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;align-items:start">
        <div class="data-card" style="margin:0">
          <div class="data-card-head">
            <h3>Demandes récentes</h3>
            <a href="/rh" style="font-size:.8rem;color:var(--forest);text-decoration:none">Tout voir →</a>
          </div>
          <table class="tbl">
            <thead>
              <tr><th>Employé</th><th>Type</th><th>Durée</th><th>Statut</th></tr>
            </thead>
            <tbody>
            <?php if (empty($recentDemandes)): ?>
              <tr><td colspan="4" class="td-muted">Aucune demande récente.</td></tr>
            <?php endif; ?>
            <?php foreach ($recentDemandes as $d): ?>
              <?php
                $stat = (string) ($d['statut'] ?? '');
                $statusClass = $stat === 'approuvee' ? 's-approuvee' : ($stat === 'refusee' ? 's-refusee' : ($stat === 'annulee' ? 's-annulee' : 's-attente'));
                $typeNom = (string) ($d['type_conge_nom'] ?? '');
                $typeLower = strtolower($typeNom);
                $typeClass = str_contains($typeLower, 'annuel') ? 't-annuel' : (str_contains($typeLower, 'maladie') ? 't-maladie' : (str_contains($typeLower, 'exception') || str_contains($typeLower, 'special') ? 't-special' : 't-sans-solde'));
                $initials = strtoupper(substr((string) ($d['prenom'] ?? ''), 0, 1) . substr((string) ($d['nom'] ?? ''), 0, 1));
              ?>
              <tr>
                <td><div style="display:flex;align-items:center;gap:7px"><div class="avatar av-green" style="width:28px;height:28px;font-size:.62rem"><?= esc($initials !== '' ? $initials : 'EM') ?></div><span class="td-name" style="font-size:.84rem"><?= esc((string) (($d['prenom'] ?? '') . ' ' . ($d['nom'] ?? ''))) ?></span></div></td>
                <td><span class="type-badge <?= esc($typeClass) ?>"><?= esc($typeNom) ?></span></td>
                <td class="td-mono"><?= esc((string) ($d['nb_jours'] ?? 0)) ?> j</td>
                <td><span class="statut <?= esc($statusClass) ?>"><?= esc($stat) ?></span></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div style="display:flex;flex-direction:column;gap:1rem">
          <div class="data-card" style="margin:0">
            <div class="data-card-head"><h3><i class="bi bi-person-slash" style="color:var(--muted);margin-right:5px"></i>Absences du mois</h3></div>
            <div style="padding:.75rem 1.1rem;display:flex;flex-direction:column;gap:.6rem">
              <?php if (empty($absentsList)): ?>
                <div class="td-muted">Aucune absence ce mois.</div>
              <?php endif; ?>
              <?php foreach ($absentsList as $a): ?>
                <?php $initials = strtoupper(substr((string) ($a['prenom'] ?? ''), 0, 1) . substr((string) ($a['nom'] ?? ''), 0, 1)); ?>
                <div style="display:flex;align-items:center;gap:8px">
                  <div class="avatar av-green" style="width:30px;height:30px;font-size:.65rem"><?= esc($initials !== '' ? $initials : 'EM') ?></div>
                  <div>
                    <div style="font-size:.83rem;font-weight:500;color:var(--ink)"><?= esc((string) (($a['prenom'] ?? '') . ' ' . ($a['nom'] ?? ''))) ?></div>
                    <div style="font-size:.72rem;color:var(--muted)"><?= esc((string) ($a['type_conge_nom'] ?? 'Congé')) ?> · retour <?= esc((string) ($a['date_fin'] ?? '')) ?></div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="flash flash-warn" style="margin:0">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span style="font-size:.8rem"><?= esc((string) $metrics['absents']) ?> absences approuvees ce mois.</span>
          </div>
        </div>
      </div>
    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> <?= esc((string) date('Y')) ?> <span>TechMada RH</span></div>
  </div>
</div>
</body>
</html>