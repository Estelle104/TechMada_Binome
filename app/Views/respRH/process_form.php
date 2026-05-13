<!DOCTYPE html>
<html lang="fr">
<?= view('partials/template_head', ['title' => 'Traitement RH - TechMada RH']) ?>
<body>
<?php
$mode = $mode ?? 'approve';
$demande = $demande ?? [];
$isReject = $mode === 'reject';
$restants = (int) ($demande['jours_restants'] ?? 0);
$demandeJours = (int) ($demande['nb_jours'] ?? 0);
?>
<div class="app-wrap">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-logo-icon"><i class="bi bi-person-check"></i></div>
            <div class="sidebar-brand-name">TechMada RH<span>Espace responsable</span></div>
        </div>
        <ul class="sidebar-nav" style="margin-top:1rem">
            <li><a href="/rh" class="active"><i class="bi bi-inbox"></i> Demandes à traiter</a></li>
        </ul>
    </aside>

    <div class="main">
        <div class="topbar">
            <div>
                <div class="topbar-title"><?= $isReject ? 'Confirmer le refus' : 'Confirmer l approbation' ?></div>
                <div class="topbar-breadcrumb"><a href="/rh">Demandes</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Traitement</div>
            </div>
        </div>

        <div class="content">
            <div class="form-section" style="border-color:<?= $isReject ? 'var(--danger-br)' : 'var(--success-br)' ?>;background:<?= $isReject ? 'var(--danger-bg)' : 'var(--success-bg)' ?>">
                <h3 style="color:<?= $isReject ? 'var(--danger)' : 'var(--success)' ?>">
                    <i class="bi <?= $isReject ? 'bi-x-circle' : 'bi-check-circle' ?>"></i>
                    <?= $isReject ? 'Confirmer le refus' : 'Confirmer l approbation' ?> —
                    <?= esc((string) (($demande['employe_prenom'] ?? '') . ' ' . ($demande['employe_nom'] ?? ''))) ?>
                </h3>

                <div style="font-size:.875rem;color:var(--ink);margin-bottom:1rem">
                    Demande de <strong><?= esc((string) $demandeJours) ?> jours</strong> du <?= esc((string) ($demande['date_debut'] ?? '')) ?> au <?= esc((string) ($demande['date_fin'] ?? '')) ?> · Type : <?= esc((string) ($demande['type_conge_nom'] ?? '')) ?><br>
                    <span style="font-size:.8rem;color:var(--muted)">Solde disponible : <?= esc((string) $restants) ?> jour(s)</span>
                </div>

                <form method="post" action="<?= $isReject ? '/rh/reject/' : '/rh/approve/' ?><?= esc((string) ($demande['id'] ?? 0)) ?>">
                    <?= csrf_field() ?>
                    <div class="f-group">
                        <label class="f-label">Commentaire RH <?= $isReject ? '(recommandé)' : '(optionnel)' ?></label>
                        <textarea class="f-textarea" name="commentaire_rh" placeholder="Ajoutez un commentaire pour l employé"></textarea>
                    </div>
                    <div class="form-actions">
                        <?php if ($isReject): ?>
                            <button class="btn-sm btn-refuse" style="padding:9px 16px;font-size:.875rem" type="submit"><i class="bi bi-x-lg"></i> Confirmer le refus</button>
                        <?php else: ?>
                            <button class="btn-sm btn-approve" style="padding:9px 16px;font-size:.875rem" type="submit"><i class="bi bi-check-lg"></i> Confirmer l approbation</button>
                        <?php endif; ?>
                        <a href="/rh" class="btn-secondary"><i class="bi bi-arrow-left"></i> Annuler</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="footer-app"><i class="bi bi-c-circle"></i> <?= esc((string) date('Y')) ?> <span>TechMada RH</span></div>
    </div>
</div>
</body>
</html>
