<!DOCTYPE html>
<html lang="fr">
<?= view('partials/template_head', ['title' => 'Soldes annuels - TechMada RH']) ?>
<body>
<?php
$annee = $annee ?? (int) date('Y');
$employes = $employes ?? [];
$types = $types ?? [];
$soldes = $soldes ?? [];
$flashSuccess = session()->getFlashdata('success');
$flashError = session()->getFlashdata('error');
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
      <li><a href="/admin/conges"><i class="bi bi-inbox"></i> Toutes les demandes</a></li>
      <li><a href="/admin/employes"><i class="bi bi-people"></i> Employes</a></li>
      <li><a href="/admin/departements"><i class="bi bi-building"></i> Departements</a></li>
      <li><a href="/admin/types"><i class="bi bi-tags"></i> Types de conge</a></li>
      <li><a href="/admin/soldes" class="active"><i class="bi bi-sliders"></i> Soldes annuels</a></li>
    </ul>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Soldes annuels</div>
        <div class="topbar-breadcrumb"><a href="/admin">Admin</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Soldes</div>
      </div>
    </div>

    <div class="content">
      <?php if ($flashSuccess): ?>
        <div class="flash flash-success"><i class="bi bi-check-circle-fill"></i> <?= esc((string) $flashSuccess) ?></div>
      <?php endif; ?>
      <?php if ($flashError): ?>
        <div class="flash flash-error"><i class="bi bi-exclamation-circle-fill"></i> <?= esc((string) $flashError) ?></div>
      <?php endif; ?>

      <div class="form-section">
        <h3><i class="bi bi-sliders" style="color:var(--forest);margin-right:6px"></i>Initialiser / ajuster un solde</h3>
        <form method="post" action="/admin/soldes/save">
          <?= csrf_field() ?>
          <div class="form-grid-2" style="margin-bottom:1rem">
            <div class="f-group">
              <label class="f-label">Employe</label>
              <select name="employe_id" class="f-select" required>
                <option value="">-- Choisir --</option>
                <?php foreach ($employes as $emp): ?>
                  <option value="<?= esc((string) $emp['id']) ?>">
                    <?= esc((string) ($emp['prenom'] . ' ' . $emp['nom'])) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="f-group">
              <label class="f-label">Type de conge</label>
              <select name="type_conge_id" class="f-select" required>
                <option value="">-- Choisir --</option>
                <?php foreach ($types as $type): ?>
                  <option value="<?= esc((string) $type['id']) ?>"><?= esc((string) $type['nom']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="f-group">
              <label class="f-label">Annee</label>
              <input type="number" name="annee" class="f-input" value="<?= esc((string) $annee) ?>" required />
            </div>
            <div class="f-group">
              <label class="f-label">Jours attribues</label>
              <input type="number" name="jours_attribues" class="f-input" value="0" required />
            </div>
            <div class="f-group">
              <label class="f-label">Jours pris</label>
              <input type="number" name="jours_pris" class="f-input" value="0" required />
            </div>
          </div>
          <div class="form-actions">
            <button class="btn-forest" type="submit"><i class="bi bi-check2"></i> Enregistrer</button>
          </div>
        </form>
      </div>

      <div class="data-card">
        <div class="data-card-head">
          <h3>Soldes de l'annee <?= esc((string) $annee) ?></h3>
        </div>
        <table class="tbl">
          <thead>
            <tr><th>Employe</th><th>Type</th><th>Attribues</th><th>Pris</th><th>Restants</th></tr>
          </thead>
          <tbody>
          <?php if (empty($soldes)): ?>
            <tr><td colspan="5" class="td-muted">Aucun solde.</td></tr>
          <?php endif; ?>
          <?php foreach ($soldes as $s): ?>
            <?php
              $attribues = (int) $s['jours_attribues'];
              $pris = (int) $s['jours_pris'];
              $restants = max(0, $attribues - $pris);
            ?>
            <tr>
              <td><?= esc((string) ($s['employe_prenom'] . ' ' . $s['employe_nom'])) ?></td>
              <td><?= esc((string) $s['type_conge_nom']) ?></td>
              <td class="td-mono"><?= esc((string) $attribues) ?> j</td>
              <td class="td-mono"><?= esc((string) $pris) ?> j</td>
              <td class="td-mono"><?= esc((string) $restants) ?> j</td>
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
