<!DOCTYPE html>
<html lang="fr">
<?= view('partials/template_head', ['title' => 'Nouvelle demande - TechMada RH']) ?>
<body>
<?php
$types = $types ?? [];
$flashError = session()->getFlashdata('error');
$flashSuccess = session()->getFlashdata('success');
$oldInput = session()->getFlashdata('_ci_old_input') ?? [];
$dateDebut = (string) ($oldInput['date_debut'] ?? '');
$dateFin = (string) ($oldInput['date_fin'] ?? '');
$oldType = (string) ($oldInput['type_conge_id'] ?? '');
$oldMotif = (string) ($oldInput['motif'] ?? '');
$computedDays = null;
$computedLabel = '';
if ($dateDebut !== '' && $dateFin !== '') {
    $start = DateTime::createFromFormat('Y-m-d', $dateDebut);
    $end = DateTime::createFromFormat('Y-m-d', $dateFin);
    if ($start && $end && $start <= $end) {
        $computedDays = (int) $end->diff($start)->format('%a') + 1;
        $computedLabel = 'du ' . $start->format('d/m/Y') . ' au ' . $end->format('d/m/Y');
    }
}
?>
<div class="app-wrap">
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div>
    </div>
    <ul class="sidebar-nav" style="margin-top:1rem">
      <li><a href="/employer"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
      <li><a href="/employer/conges/nouvelle" class="active"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
      <li><a href="/employer/conges/mes"><i class="bi bi-calendar3"></i> Mes demandes</a></li>
      <li><a href="/employer/profil"><i class="bi bi-person"></i> Mon profil</a></li>
      <li><a href="/employer/calendrier"><i class="bi bi-calendar"></i> Calendrier</a></li>

    </ul>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Nouvelle demande de congé</div>
        <div class="topbar-breadcrumb">
          <a href="/employer">Accueil</a>
          <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Nouvelle demande
        </div>
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

      <div style="display:grid;grid-template-columns:1fr 300px;gap:1.5rem;align-items:start" class="form-layout">
        <div>
          <form class="form-section" method="post" action="/employer/conges">
            <?= csrf_field() ?>
            <h3>Détails de la demande</h3>

            <div class="f-group" style="margin-bottom:1rem">
              <label class="f-label">Type de congé <span style="color:var(--danger)">*</span></label>
              <select class="f-select" name="type_conge_id" required>
                <option value="">-- Choisir un type --</option>
                <?php foreach ($types as $type): ?>
                  <option value="<?= esc((string) $type['id']) ?>" <?= (string) $type['id'] === $oldType ? 'selected' : '' ?>>
                    <?= esc((string) $type['nom']) ?> (<?= esc((string) $type['restants']) ?> j restants)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-grid-2" style="margin-bottom:1rem">
              <div class="f-group">
                <label class="f-label">Date de début <span style="color:var(--danger)">*</span></label>
                <input type="date" class="f-input" name="date_debut" value="<?= esc($dateDebut) ?>" required />
              </div>
              <div class="f-group">
                <label class="f-label">Date de fin <span style="color:var(--danger)">*</span></label>
                <input type="date" class="f-input" name="date_fin" value="<?= esc($dateFin) ?>" required />
              </div>
            </div>

            <?php if ($computedDays !== null): ?>
              <div class="f-computed">
                <div class="f-computed-num"><?= esc((string) $computedDays) ?></div>
                <div class="f-computed-label">jours calendaires calculés<br><span style="font-size:.7rem;opacity:.7"><?= esc($computedLabel) ?></span></div>
              </div>
            <?php endif; ?>

            <div class="f-group" style="margin-bottom:1rem">
              <label class="f-label">Motif (optionnel)</label>
              <textarea class="f-textarea" name="motif" placeholder="Précisez le motif de votre demande si nécessaire..."><?= esc($oldMotif) ?></textarea>
              <div class="f-hint">Le motif est visible par le responsable RH.</div>
            </div>

            <div class="form-actions">
              <button class="btn-forest" type="submit"><i class="bi bi-send"></i> Soumettre la demande</button>
              <a href="/employer" class="btn-secondary"><i class="bi bi-x"></i> Annuler</a>
            </div>
          </form>
        </div>

        <div style="display:flex;flex-direction:column;gap:1rem">
          <div class="data-card" style="margin:0">
            <div class="data-card-head"><h3><i class="bi bi-piggy-bank" style="color:var(--forest);margin-right:5px"></i>Vos soldes actuels</h3></div>
            <div style="padding:.75rem 1.1rem;display:flex;flex-direction:column;gap:.75rem">
              <?php if (empty($types)): ?>
                <div class="empty"><i class="bi bi-inbox"></i><p>Aucun solde disponible.</p></div>
              <?php endif; ?>
              <?php foreach ($types as $type): ?>
                <?php
                  $attribues = (int) $type['attribues'];
                  $restants = (int) $type['restants'];
                  $percent = $attribues > 0 ? (int) round(($restants / $attribues) * 100) : 0;
                  $fillClass = $percent <= 20 ? 'danger' : ($percent <= 40 ? 'warn' : '');
                ?>
                <div>
                  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                    <span style="font-size:.8rem;color:var(--ink)"><?= esc((string) $type['nom']) ?></span>
                    <span style="font-family:'DM Mono',monospace;font-size:.8rem;color:var(--forest);font-weight:500"><?= esc((string) $restants) ?> j</span>
                  </div>
                  <div class="solde-bar"><div class="solde-fill <?= esc($fillClass) ?>" style="width:<?= esc((string) $percent) ?>%"></div></div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="flash flash-info" style="margin:0">
            <i class="bi bi-info-circle-fill"></i>
            <span style="font-size:.8rem">Le solde est déduit uniquement à l'approbation de votre responsable.</span>
          </div>
        </div>
      </div>
    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> <?= esc((string) date('Y')) ?> <span>TechMada RH</span></div>
  </div>
</div>
</body>
</html>
