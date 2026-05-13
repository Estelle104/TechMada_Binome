<!DOCTYPE html>
<html lang="fr">
<?= view('partials/template_head', ['title' => 'Employes - TechMada RH']) ?>
<body>
<?php
$employes = $employes ?? [];
$departements = $departements ?? [];
$editEmploye = $editEmploye ?? null;
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
      <li><a href="/admin/employes" class="active"><i class="bi bi-people"></i> Employes</a></li>
      <li><a href="/admin/departements"><i class="bi bi-building"></i> Departements</a></li>
      <li><a href="/admin/types"><i class="bi bi-tags"></i> Types de conge</a></li>
      <li><a href="/admin/soldes"><i class="bi bi-sliders"></i> Soldes annuels</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar" style="background:#5a2d82;width:32px;height:32px;font-size:.7rem">AD</div>
        <div><div class="user-name">Administrateur</div><div class="user-role">Admin systeme</div></div>
        <a href="/logout" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Gestion des employes</div>
        <div class="topbar-breadcrumb"><a href="/admin">Admin</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Employes</div>
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
        <h3><i class="bi bi-person-plus" style="color:var(--forest);margin-right:6px"></i>Ajouter un employe</h3>
        <form method="post" action="/admin/employes/create">
          <?= csrf_field() ?>
          <div class="form-grid-2" style="margin-bottom:1rem">
            <div class="f-group">
              <label class="f-label">Prenom</label>
              <input type="text" name="prenom" class="f-input" required />
            </div>
            <div class="f-group">
              <label class="f-label">Nom</label>
              <input type="text" name="nom" class="f-input" required />
            </div>
            <div class="f-group">
              <label class="f-label">Email</label>
              <input type="email" name="email" class="f-input" required />
            </div>
            <div class="f-group">
              <label class="f-label">Mot de passe initial</label>
              <input type="password" name="mot_de_passe" class="f-input" required />
            </div>
            <div class="f-group">
              <label class="f-label">Departement</label>
              <select name="departement_id" class="f-select">
                <option value="">Sans departement</option>
                <?php foreach ($departements as $dep): ?>
                  <option value="<?= esc((string) $dep['id']) ?>"><?= esc((string) $dep['nom']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="f-group">
              <label class="f-label">Role</label>
              <select name="role" class="f-select" required>
                <option value="employe">Employe</option>
                <option value="rh">Responsable RH</option>
                <option value="admin">Administrateur</option>
              </select>
            </div>
            <div class="f-group">
              <label class="f-label">Date d'embauche</label>
              <input type="date" name="date_embauche" class="f-input" required />
            </div>
          </div>
          <div class="form-actions">
            <button class="btn-forest" type="submit"><i class="bi bi-plus"></i> Creer l'employe</button>
          </div>
        </form>
      </div>

      <?php if ($editEmploye): ?>
        <div class="form-section">
          <h3><i class="bi bi-pencil" style="color:var(--info);margin-right:6px"></i>Editer l'employe</h3>
          <form method="post" action="/admin/employes/update/<?= esc((string) $editEmploye['id']) ?>">
            <?= csrf_field() ?>
            <div class="form-grid-2" style="margin-bottom:1rem">
              <div class="f-group">
                <label class="f-label">Prenom</label>
                <input type="text" name="prenom" class="f-input" value="<?= esc((string) $editEmploye['prenom']) ?>" required />
              </div>
              <div class="f-group">
                <label class="f-label">Nom</label>
                <input type="text" name="nom" class="f-input" value="<?= esc((string) $editEmploye['nom']) ?>" required />
              </div>
              <div class="f-group">
                <label class="f-label">Email</label>
                <input type="email" name="email" class="f-input" value="<?= esc((string) $editEmploye['email']) ?>" required />
              </div>
              <div class="f-group">
                <label class="f-label">Nouveau mot de passe (optionnel)</label>
                <input type="password" name="mot_de_passe" class="f-input" />
              </div>
              <div class="f-group">
                <label class="f-label">Departement</label>
                <select name="departement_id" class="f-select">
                  <option value="">Sans departement</option>
                  <?php foreach ($departements as $dep): ?>
                    <option value="<?= esc((string) $dep['id']) ?>" <?= (string) $editEmploye['departement_id'] === (string) $dep['id'] ? 'selected' : '' ?>>
                      <?= esc((string) $dep['nom']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="f-group">
                <label class="f-label">Role</label>
                <select name="role" class="f-select" required>
                  <option value="employe" <?= $editEmploye['role'] === 'employe' ? 'selected' : '' ?>>Employe</option>
                  <option value="rh" <?= $editEmploye['role'] === 'rh' ? 'selected' : '' ?>>Responsable RH</option>
                  <option value="admin" <?= $editEmploye['role'] === 'admin' ? 'selected' : '' ?>>Administrateur</option>
                </select>
              </div>
              <div class="f-group">
                <label class="f-label">Date d'embauche</label>
                <input type="date" name="date_embauche" class="f-input" value="<?= esc((string) $editEmploye['date_embauche']) ?>" required />
              </div>
              <div class="f-group">
                <label class="f-label">Statut</label>
                <select name="actif" class="f-select">
                  <option value="1" <?= (int) $editEmploye['actif'] === 1 ? 'selected' : '' ?>>Actif</option>
                  <option value="0" <?= (int) $editEmploye['actif'] === 0 ? 'selected' : '' ?>>Inactif</option>
                </select>
              </div>
            </div>
            <div class="form-actions">
              <button class="btn-forest" type="submit"><i class="bi bi-check2"></i> Enregistrer</button>
              <a href="/admin/employes" class="btn-secondary"><i class="bi bi-x"></i> Annuler</a>
            </div>
          </form>
        </div>
      <?php endif; ?>

      <div class="data-card">
        <div class="data-card-head">
          <h3>Tous les employes</h3>
        </div>
        <table class="tbl">
          <thead>
            <tr><th>Employe</th><th>Departement</th><th>Role</th><th>Embauche</th><th>Statut</th><th>Actions</th></tr>
          </thead>
          <tbody>
          <?php if (empty($employes)): ?>
            <tr><td colspan="6" class="td-muted">Aucun employe.</td></tr>
          <?php endif; ?>
          <?php foreach ($employes as $emp): ?>
            <?php
              $initials = strtoupper(substr((string) $emp['prenom'], 0, 1) . substr((string) $emp['nom'], 0, 1));
              $isActive = (int) ($emp['actif'] ?? 0) === 1;
            ?>
            <tr>
              <td>
                <div class="profile-row">
                  <div class="avatar av-green" style="width:32px;height:32px;font-size:.68rem"><?= esc($initials !== '' ? $initials : 'EM') ?></div>
                  <div class="profile-info">
                    <div class="pname"><?= esc((string) ($emp['prenom'] . ' ' . $emp['nom'])) ?></div>
                    <div class="pdept"><?= esc((string) $emp['email']) ?></div>
                  </div>
                </div>
              </td>
              <td class="td-muted"><?= esc((string) ($emp['departement_nom'] ?? 'Sans departement')) ?></td>
              <td><span class="type-badge" style="background:#f1efe8;color:#444441"><?= esc((string) $emp['role']) ?></span></td>
              <td class="td-muted td-mono" style="font-size:.78rem"><?= esc((string) $emp['date_embauche']) ?></td>
              <td><span class="statut <?= $isActive ? 's-approuvee' : 's-annulee' ?>" style="font-size:.68rem"><?= $isActive ? 'actif' : 'inactif' ?></span></td>
              <td>
                <div class="action-btns">
                  <a class="btn-sm btn-edit" href="/admin/employes?edit=<?= esc((string) $emp['id']) ?>"><i class="bi bi-pencil"></i> Editer</a>
                  <?php if ($isActive): ?>
                    <form method="post" action="/admin/employes/deactivate/<?= esc((string) $emp['id']) ?>" style="margin:0">
                      <?= csrf_field() ?>
                      <button class="btn-sm btn-del" type="submit"><i class="bi bi-slash-circle"></i> Desactiver</button>
                    </form>
                  <?php else: ?>
                    <span class="td-muted" style="font-size:.75rem">—</span>
                  <?php endif; ?>
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
