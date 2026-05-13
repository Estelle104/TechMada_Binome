<!DOCTYPE html>
<html lang="fr">
<?= view('partials/template_head', ['title' => 'Departements - TechMada RH']) ?>
<body>
<?php
$departements = $departements ?? [];
$editDepartement = $editDepartement ?? null;
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
      <li><a href="/admin/departements" class="active"><i class="bi bi-building"></i> Departements</a></li>
      <li><a href="/admin/types"><i class="bi bi-tags"></i> Types de conge</a></li>
      <li><a href="/admin/soldes"><i class="bi bi-sliders"></i> Soldes annuels</a></li>
    </ul>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Departements</div>
        <div class="topbar-breadcrumb"><a href="/admin">Admin</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Departements</div>
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
        <h3><i class="bi bi-plus-circle" style="color:var(--forest);margin-right:6px"></i>Ajouter un departement</h3>
        <form method="post" action="/admin/departements/create">
          <?= csrf_field() ?>
          <div class="form-grid-2" style="margin-bottom:1rem">
            <div class="f-group">
              <label class="f-label">Nom</label>
              <input type="text" name="nom" class="f-input" required />
            </div>
            <div class="f-group">
              <label class="f-label">Description</label>
              <input type="text" name="description" class="f-input" />
            </div>
          </div>
          <div class="form-actions">
            <button class="btn-forest" type="submit"><i class="bi bi-plus"></i> Creer</button>
          </div>
        </form>
      </div>

      <?php if ($editDepartement): ?>
        <div class="form-section">
          <h3><i class="bi bi-pencil" style="color:var(--info);margin-right:6px"></i>Editer le departement</h3>
          <form method="post" action="/admin/departements/update/<?= esc((string) $editDepartement['id']) ?>">
            <?= csrf_field() ?>
            <div class="form-grid-2" style="margin-bottom:1rem">
              <div class="f-group">
                <label class="f-label">Nom</label>
                <input type="text" name="nom" class="f-input" value="<?= esc((string) $editDepartement['nom']) ?>" required />
              </div>
              <div class="f-group">
                <label class="f-label">Description</label>
                <input type="text" name="description" class="f-input" value="<?= esc((string) $editDepartement['description'] ?? '') ?>" />
              </div>
            </div>
            <div class="form-actions">
              <button class="btn-forest" type="submit"><i class="bi bi-check2"></i> Enregistrer</button>
              <a href="/admin/departements" class="btn-secondary"><i class="bi bi-x"></i> Annuler</a>
            </div>
          </form>
        </div>
      <?php endif; ?>

      <div class="data-card">
        <div class="data-card-head"><h3>Liste des departements</h3></div>
        <table class="tbl">
          <thead>
            <tr><th>Nom</th><th>Description</th><th>Actions</th></tr>
          </thead>
          <tbody>
          <?php if (empty($departements)): ?>
            <tr><td colspan="3" class="td-muted">Aucun departement.</td></tr>
          <?php endif; ?>
          <?php foreach ($departements as $dep): ?>
            <tr>
              <td class="td-name"><?= esc((string) $dep['nom']) ?></td>
              <td class="td-muted"><?= esc((string) ($dep['description'] ?? '')) ?></td>
              <td>
                <div class="action-btns">
                  <a class="btn-sm btn-edit" href="/admin/departements?edit=<?= esc((string) $dep['id']) ?>"><i class="bi bi-pencil"></i> Editer</a>
                  <form method="post" action="/admin/departements/delete/<?= esc((string) $dep['id']) ?>" style="margin:0">
                    <?= csrf_field() ?>
                    <button class="btn-sm btn-del" type="submit"><i class="bi bi-trash"></i> Supprimer</button>
                  </form>
                </div>
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
