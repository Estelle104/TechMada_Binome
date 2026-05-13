<!DOCTYPE html>
<html lang="fr">
<?= view('partials/template_head', ['title' => 'Mes demandes - TechMada RH']) ?>
<body>
<?php
$statut = $statut ?? 'all';
$conges = $conges ?? [];
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
      <li><a href="/employer/conges/mes" class="active"><i class="bi bi-calendar3"></i> Mes demandes</a></li>
      <li><a href="/employer/profil"><i class="bi bi-person"></i> Mon profil</a></li>
    </ul>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Mes demandes de congé</div>
        <div class="topbar-breadcrumb"><a href="/employer">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Mes demandes</div>
      </div>
      <div class="topbar-actions">
        <a href="/employer/conges/nouvelle" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-plus-lg"></i> Nouvelle demande</a>
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
        <div class="data-card-head">
          <h3>Toutes mes demandes</h3>
          <form method="get" action="/employer/conges/mes">
            <select class="f-select" name="statut" style="font-size:.8rem;padding:6px 10px;width:auto" onchange="this.form.submit()">
              <option value="all" <?= $statut === 'all' ? 'selected' : '' ?>>Tous les statuts</option>
              <option value="en_attente" <?= $statut === 'en_attente' ? 'selected' : '' ?>>En attente</option>
              <option value="approuvee" <?= $statut === 'approuvee' ? 'selected' : '' ?>>Approuvée</option>
              <option value="refusee" <?= $statut === 'refusee' ? 'selected' : '' ?>>Refusée</option>
              <option value="annulee" <?= $statut === 'annulee' ? 'selected' : '' ?>>Annulée</option>
            </select>
          </form>
        </div>
        <table class="tbl">
          <thead>
            <tr><th>Type</th><th>Début</th><th>Fin</th><th>Durée</th><th>Statut</th><th>Commentaire RH</th><th>Action</th></tr>
          </thead>
          <tbody>
          <?php if (empty($conges)): ?>
            <tr><td colspan="7" class="td-muted">Aucune demande trouvée.</td></tr>
          <?php endif; ?>
          <?php foreach ($conges as $conge): ?>
            <?php
              $stat = (string) $conge['statut'];
              $statusClass = $stat === 'approuvee' ? 's-approuvee' : ($stat === 'refusee' ? 's-refusee' : ($stat === 'annulee' ? 's-annulee' : 's-attente'));
              $typeNom = (string) ($conge['type_conge_nom'] ?? '');
              $typeLower = strtolower($typeNom);
              $typeClass = str_contains($typeLower, 'annuel') ? 't-annuel' : (str_contains($typeLower, 'maladie') ? 't-maladie' : (str_contains($typeLower, 'exception') || str_contains($typeLower, 'special') ? 't-special' : 't-sans-solde'));
            ?>
            <tr>
              <td><span class="type-badge <?= esc($typeClass) ?>"><?= esc($typeNom) ?></span></td>
              <td class="td-muted"><?= esc(date('d/m/Y', strtotime((string) $conge['date_debut']))) ?></td>
              <td class="td-muted"><?= esc(date('d/m/Y', strtotime((string) $conge['date_fin']))) ?></td>
              <td class="td-mono"><?= esc((string) $conge['nb_jours']) ?> j</td>
              <td><span class="statut <?= esc($statusClass) ?>"><?= esc($stat) ?></span></td>
              <td class="td-muted" style="font-size:.78rem">
                <?= esc((string) ($conge['commentaire_rh'] ?? '—')) ?>
              </td>
              <td>
                <?php if ($stat === 'en_attente'): ?>
                  <form method="post" action="/employer/conges/annuler/<?= esc((string) $conge['id']) ?>" style="margin:0">
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
    <div class="footer-app"><i class="bi bi-c-circle"></i> <?= esc((string) date('Y')) ?> <span>TechMada RH</span></div>
  </div>
</div>
</body>
</html>
 