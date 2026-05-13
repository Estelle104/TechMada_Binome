<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Responsable RH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --ink: #1c2b1e;
            --forest: #2d5a3d;
            --forest2: #3d7a52;
            --cream: #f8f6f1;
            --white: #ffffff;
            --border: #dde8e1;
            --muted: #7a8f80;
            --danger: #c0392b;
            --danger-bg: #fdf0ee;
            --success: #1e6b3f;
            --success-bg: #edf7f2;
            --warn: #b8750a;
            --warn-bg: #fef9ee;
            --info-bg: #eaf2fb;
        }
        body { background: var(--cream); color: var(--ink); font-family: Arial, sans-serif; }
        .wrap { max-width: 1200px; margin: 0 auto; padding: 1.2rem; }
        .head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .title { font-size: 1.4rem; font-weight: 700; }
        .card-box { background: var(--white); border: 1px solid var(--border); border-radius: 12px; }
        .metrics { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: .9rem; margin-bottom: 1rem; }
        .metric { padding: 1rem; }
        .metric .n { font-size: 1.5rem; font-weight: 700; }
        .filter-row { display: flex; gap: .5rem; flex-wrap: wrap; margin-bottom: 1rem; }
        .pill { border-radius: 20px; border: 1px solid var(--border); background: #fff; padding: 5px 12px; text-decoration: none; color: var(--muted); font-size: .85rem; }
        .pill.active { background: var(--forest); border-color: var(--forest); color: #fff; }
        .tbl { width: 100%; border-collapse: collapse; }
        .tbl th, .tbl td { border-bottom: 1px solid var(--border); padding: 10px 12px; font-size: .9rem; }
        .tbl th { background: var(--cream); text-transform: uppercase; letter-spacing: .04em; font-size: .72rem; color: var(--muted); }
        .badge-s { padding: 4px 10px; border-radius: 12px; font-size: .75rem; }
        .attente { background: var(--warn-bg); color: var(--warn); }
        .approuvee { background: var(--success-bg); color: var(--success); }
        .refusee { background: var(--danger-bg); color: var(--danger); }
        .btn-a { border: 1px solid #8fd4aa; color: var(--success); background: var(--success-bg); border-radius: 6px; padding: 4px 8px; text-decoration: none; font-size: .8rem; }
        .btn-r { border: 1px solid #f0b8b2; color: var(--danger); background: var(--danger-bg); border-radius: 6px; padding: 4px 8px; text-decoration: none; font-size: .8rem; }
        .muted { color: var(--muted); }
        .ok { color: var(--success); font-weight: 700; }
        .ko { color: var(--danger); font-weight: 700; }
        .flash { padding: .7rem .9rem; border-radius: 8px; margin-bottom: .8rem; }
        .flash-ok { background: var(--success-bg); color: var(--success); }
        .flash-err { background: var(--danger-bg); color: var(--danger); }
    </style>
</head>
<body>
<?php
$counts = $counts ?? ['total' => 0, 'en_attente' => 0, 'approuvee' => 0, 'refusee' => 0];
$filters = $filters ?? ['statut' => 'all', 'departement_id' => 'all'];
$departements = $departements ?? [];
$demandes = $demandes ?? [];

$flashSuccess = session()->getFlashdata('success');
$flashError = session()->getFlashdata('error');
?>
<div class="wrap">
    <div class="head">
        <div>
            <div class="title">Demandes a traiter - Responsable RH</div>
            <div class="muted">Validation des conges avec mise a jour automatique du solde</div>
        </div>
        <a href="/logout" class="btn btn-outline-secondary btn-sm"><i class="bi bi-box-arrow-right"></i> Deconnexion</a>
    </div>

    <?php if ($flashSuccess): ?>
        <div class="flash flash-ok"><i class="bi bi-check-circle"></i> <?= esc((string) $flashSuccess) ?></div>
    <?php endif; ?>
    <?php if ($flashError): ?>
        <div class="flash flash-err"><i class="bi bi-exclamation-triangle"></i> <?= esc((string) $flashError) ?></div>
    <?php endif; ?>

    <div class="metrics">
        <div class="card-box metric"><div class="muted">Toutes</div><div class="n"><?= esc((string) $counts['total']) ?></div></div>
        <div class="card-box metric"><div class="muted">En attente</div><div class="n"><?= esc((string) $counts['en_attente']) ?></div></div>
        <div class="card-box metric"><div class="muted">Approuvees</div><div class="n"><?= esc((string) $counts['approuvee']) ?></div></div>
        <div class="card-box metric"><div class="muted">Refusees</div><div class="n"><?= esc((string) $counts['refusee']) ?></div></div>
    </div>

    <div class="filter-row">
        <a class="pill <?= $filters['statut'] === 'all' ? 'active' : '' ?>" href="/rh?statut=all&departement_id=<?= esc((string) $filters['departement_id']) ?>">Tous</a>
        <a class="pill <?= $filters['statut'] === 'en_attente' ? 'active' : '' ?>" href="/rh?statut=en_attente&departement_id=<?= esc((string) $filters['departement_id']) ?>">En attente</a>
        <a class="pill <?= $filters['statut'] === 'approuvee' ? 'active' : '' ?>" href="/rh?statut=approuvee&departement_id=<?= esc((string) $filters['departement_id']) ?>">Approuvees</a>
        <a class="pill <?= $filters['statut'] === 'refusee' ? 'active' : '' ?>" href="/rh?statut=refusee&departement_id=<?= esc((string) $filters['departement_id']) ?>">Refusees</a>

        <form method="get" action="/rh" style="margin-left:auto; display:flex; gap:.5rem;">
            <input type="hidden" name="statut" value="<?= esc((string) $filters['statut']) ?>">
            <select name="departement_id" class="form-select form-select-sm">
                <option value="all" <?= $filters['departement_id'] === 'all' ? 'selected' : '' ?>>Tous les departements</option>
                <?php foreach ($departements as $dep): ?>
                    <option value="<?= esc((string) $dep['id']) ?>" <?= (string) $filters['departement_id'] === (string) $dep['id'] ? 'selected' : '' ?>>
                        <?= esc((string) $dep['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-sm btn-outline-secondary" type="submit">Filtrer</button>
        </form>
    </div>

    <div class="card-box">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Employe</th>
                    <th>Type</th>
                    <th>Periode</th>
                    <th>Duree</th>
                    <th>Solde dispo</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($demandes)): ?>
                <tr><td colspan="7" class="muted">Aucune demande trouvee.</td></tr>
            <?php endif; ?>
            <?php foreach ($demandes as $d): ?>
                <?php
                    $restants = (int) ($d['jours_restants'] ?? 0);
                    $demande = (int) ($d['nb_jours'] ?? 0);
                    $enAttente = ($d['statut'] === 'en_attente');
                    $soldeOk = $restants >= $demande;
                ?>
                <tr>
                    <td>
                        <strong><?= esc((string) ($d['employe_prenom'] . ' ' . $d['employe_nom'])) ?></strong><br>
                        <span class="muted"><?= esc((string) ($d['departement_nom'] ?? 'Sans departement')) ?></span>
                    </td>
                    <td><?= esc((string) $d['type_conge_nom']) ?></td>
                    <td><?= esc((string) $d['date_debut']) ?> -> <?= esc((string) $d['date_fin']) ?></td>
                    <td><?= esc((string) $d['nb_jours']) ?> j</td>
                    <td>
                        <span class="<?= $soldeOk ? 'ok' : 'ko' ?>"><?= esc((string) $restants) ?> j</span>
                        <?php if (!$soldeOk): ?><span class="muted"> insuffisant</span><?php endif; ?>
                    </td>
                    <td>
                        <?php if ($d['statut'] === 'en_attente'): ?>
                            <span class="badge-s attente">en attente</span>
                        <?php elseif ($d['statut'] === 'approuvee'): ?>
                            <span class="badge-s approuvee">approuvee</span>
                        <?php elseif ($d['statut'] === 'refusee'): ?>
                            <span class="badge-s refusee">refusee</span>
                        <?php else: ?>
                            <span class="badge-s"><?= esc((string) $d['statut']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($enAttente): ?>
                            <?php if ($soldeOk): ?>
                                <a class="btn-a" href="/rh/approve/<?= esc((string) $d['id']) ?>"><i class="bi bi-check-lg"></i> Approuver</a>
                            <?php else: ?>
                                <span class="btn-a" style="opacity:.35;pointer-events:none"><i class="bi bi-check-lg"></i> Approuver</span>
                            <?php endif; ?>
                            <a class="btn-r" href="/rh/reject/<?= esc((string) $d['id']) ?>"><i class="bi bi-x-lg"></i> Refuser</a>
                        <?php else: ?>
                            <span class="muted">Traitee</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
