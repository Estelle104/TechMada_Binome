<!DOCTYPE html>
<html lang="fr">
<?= view('partials/template_head', ['title' => 'Demandes RH - TechMada RH']) ?>
<body>
<?php
$counts = $counts ?? ['total' => 0, 'en_attente' => 0, 'approuvee' => 0, 'refusee' => 0];
$filters = $filters ?? ['statut' => 'all', 'departement_id' => 'all'];
$departements = $departements ?? [];
$demandes = $demandes ?? [];
$flashSuccess = session()->getFlashdata('success');
$flashError = session()->getFlashdata('error');
?>
<div class="app-wrap">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-logo-icon"><i class="bi bi-person-check"></i></div>
            <div class="sidebar-brand-name">TechMada RH<span>Espace responsable</span></div>
        </div>
        <div class="sidebar-section">Menu</div>
        <ul class="sidebar-nav">
            <li><a href="/rh" class="active"><i class="bi bi-inbox"></i> Demandes à traiter <span class="nav-badge alert"><?= esc((string) $counts['en_attente']) ?></span></a></li>
            <li><a href="/rh"><i class="bi bi-archive"></i> Historique</a></li>
            <li><a href="/rh"><i class="bi bi-people"></i> Soldes employés</a></li>
        </ul>
        <div class="sidebar-user">
            <div class="s-user-row">
                <div class="avatar av-blue">RH</div>
                <div><div class="user-name">Responsable RH</div><div class="user-role">Validation</div></div>
                <a href="/logout" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem"><i class="bi bi-box-arrow-right"></i></a>
            </div>
        </div>
    </aside>

    <div class="main">
        <div class="topbar">
            <div>
                <div class="topbar-title">Demandes à traiter</div>
                <div class="topbar-breadcrumb"><a href="/rh">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Demandes</div>
            </div>
            <div class="topbar-actions">
                <span style="font-size:.8rem;color:var(--muted);background:var(--warn-bg);border:1px solid var(--warn-br);border-radius:6px;padding:5px 10px;display:flex;align-items:center;gap:5px;color:var(--warn)">
                    <i class="bi bi-hourglass-split"></i> <?= esc((string) $counts['en_attente']) ?> en attente
                </span>
            </div>
        </div>

        <div class="content">
            <?php if ($flashSuccess): ?>
                <div class="flash flash-success"><i class="bi bi-check-circle-fill"></i> <?= esc((string) $flashSuccess) ?></div>
            <?php endif; ?>
            <?php if ($flashError): ?>
                <div class="flash flash-error"><i class="bi bi-exclamation-circle-fill"></i> <?= esc((string) $flashError) ?></div>
            <?php endif; ?>

            <div style="display:flex;gap:8px;margin-bottom:1.25rem;flex-wrap:wrap">
                <a class="btn-secondary" style="border-radius:20px;padding:6px 14px;font-size:.8rem;<?= $filters['statut'] === 'all' ? 'background:var(--forest);color:var(--white);border-color:var(--forest)' : '' ?>" href="/rh?statut=all&departement_id=<?= esc((string) $filters['departement_id']) ?>">Tous (<?= esc((string) $counts['total']) ?>)</a>
                <a class="btn-secondary" style="border-radius:20px;padding:6px 14px;font-size:.8rem;<?= $filters['statut'] === 'en_attente' ? 'background:var(--forest);color:var(--white);border-color:var(--forest)' : '' ?>" href="/rh?statut=en_attente&departement_id=<?= esc((string) $filters['departement_id']) ?>">En attente (<?= esc((string) $counts['en_attente']) ?>)</a>
                <a class="btn-secondary" style="border-radius:20px;padding:6px 14px;font-size:.8rem;<?= $filters['statut'] === 'approuvee' ? 'background:var(--forest);color:var(--white);border-color:var(--forest)' : '' ?>" href="/rh?statut=approuvee&departement_id=<?= esc((string) $filters['departement_id']) ?>">Approuvées (<?= esc((string) $counts['approuvee']) ?>)</a>
                <a class="btn-secondary" style="border-radius:20px;padding:6px 14px;font-size:.8rem;<?= $filters['statut'] === 'refusee' ? 'background:var(--forest);color:var(--white);border-color:var(--forest)' : '' ?>" href="/rh?statut=refusee&departement_id=<?= esc((string) $filters['departement_id']) ?>">Refusées (<?= esc((string) $counts['refusee']) ?>)</a>

                <form method="get" action="/rh" style="margin-left:auto;display:flex;gap:6px">
                    <input type="hidden" name="statut" value="<?= esc((string) $filters['statut']) ?>">
                    <select name="departement_id" class="f-select" style="font-size:.8rem;padding:6px 10px;width:auto">
                        <option value="all" <?= $filters['departement_id'] === 'all' ? 'selected' : '' ?>>Tous les départements</option>
                        <?php foreach ($departements as $dep): ?>
                            <option value="<?= esc((string) $dep['id']) ?>" <?= (string) $filters['departement_id'] === (string) $dep['id'] ? 'selected' : '' ?>><?= esc((string) $dep['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn-secondary" type="submit">Filtrer</button>
                </form>
            </div>

            <div class="data-card">
                <div class="data-card-head"><h3>Toutes les demandes</h3></div>
                <table class="tbl">
                    <thead>
                        <tr><th>Employé</th><th>Type</th><th>Période</th><th>Durée</th><th>Solde dispo</th><th>Statut</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                    <?php if (empty($demandes)): ?>
                        <tr><td colspan="7" class="td-muted">Aucune demande trouvée.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($demandes as $d): ?>
                        <?php
                            $restants = (int) ($d['jours_restants'] ?? 0);
                            $demande = (int) ($d['nb_jours'] ?? 0);
                            $enAttente = ($d['statut'] === 'en_attente');
                            $soldeOk = $restants >= $demande;
                            $statutClass = $d['statut'] === 'approuvee' ? 's-approuvee' : ($d['statut'] === 'refusee' ? 's-refusee' : ($d['statut'] === 'annulee' ? 's-annulee' : 's-attente'));
                            $typeNom = (string) $d['type_conge_nom'];
                            $typeLower = strtolower($typeNom);
                            $typeClass = str_contains($typeLower, 'annuel') ? 't-annuel' : (str_contains($typeLower, 'maladie') ? 't-maladie' : (str_contains($typeLower, 'exception') || str_contains($typeLower, 'special') ? 't-special' : 't-sans-solde'));
                        ?>
                        <tr>
                            <td>
                                <div class="profile-row">
                                    <div class="avatar av-green" style="width:32px;height:32px;font-size:.7rem"><?= esc(strtoupper(substr((string) $d['employe_prenom'], 0, 1) . substr((string) $d['employe_nom'], 0, 1))) ?></div>
                                    <div class="profile-info">
                                        <div class="pname"><?= esc((string) ($d['employe_prenom'] . ' ' . $d['employe_nom'])) ?></div>
                                        <div class="pdept"><?= esc((string) ($d['departement_nom'] ?? 'Sans département')) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="type-badge <?= esc($typeClass) ?>"><?= esc($typeNom) ?></span></td>
                            <td class="td-muted" style="font-size:.8rem"><?= esc(date('d/m/Y', strtotime((string) $d['date_debut']))) ?> – <?= esc(date('d/m/Y', strtotime((string) $d['date_fin']))) ?></td>
                            <td class="td-mono"><?= esc((string) $d['nb_jours']) ?> j</td>
                            <td>
                                <span style="font-family:'DM Mono',monospace;font-size:.82rem;color:<?= $soldeOk ? 'var(--success)' : 'var(--warn)' ?>;font-weight:500"><?= esc((string) $restants) ?> j</span>
                                <?php if (!$soldeOk): ?><span style="font-size:.72rem;color:var(--danger)"> insuffisant</span><?php endif; ?>
                            </td>
                            <td><span class="statut <?= esc($statutClass) ?>"><?= esc((string) $d['statut']) ?></span></td>
                            <td>
                                <?php if ($enAttente): ?>
                                    <div class="action-btns">
                                        <?php if ($soldeOk): ?>
                                            <a class="btn-sm btn-approve" href="/rh/approve/<?= esc((string) $d['id']) ?>"><i class="bi bi-check-lg"></i> Approuver</a>
                                        <?php else: ?>
                                            <span class="btn-sm btn-approve" style="opacity:.4;pointer-events:none"><i class="bi bi-check-lg"></i> Approuver</span>
                                        <?php endif; ?>
                                        <a class="btn-sm btn-refuse" href="/rh/reject/<?= esc((string) $d['id']) ?>"><i class="bi bi-x-lg"></i> Refuser</a>
                                    </div>
                                <?php else: ?>
                                    <span class="td-muted" style="font-size:.75rem">Traitée</span>
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
