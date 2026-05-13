<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traitement RH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --danger: #c0392b;
            --danger-bg: #fdf0ee;
            --danger-br: #f0b8b2;
            --success: #1e6b3f;
            --success-bg: #edf7f2;
            --success-br: #8fd4aa;
        }
        body { background: #f8f6f1; }
        .box { max-width: 760px; margin: 2rem auto; background: #fff; border: 1px solid #dde8e1; border-radius: 12px; padding: 1.2rem; }
        .danger { background: var(--danger-bg); border-color: var(--danger-br); }
        .ok { background: var(--success-bg); border-color: var(--success-br); }
    </style>
</head>
<body>
    <?php
    $mode = $mode ?? 'approve';
    $demande = $demande ?? [];
    ?>
    <div class="box <?= $mode === 'reject' ? 'danger' : 'ok' ?>">
        <h4>
            <?= $mode === 'reject' ? 'Confirmer le refus' : 'Confirmer l approbation' ?>
            - <?= esc((string) (($demande['employe_prenom'] ?? '') . ' ' . ($demande['employe_nom'] ?? ''))) ?>
        </h4>

        <p class="mb-1"><strong>Type:</strong> <?= esc((string) ($demande['type_conge_nom'] ?? '')) ?></p>
        <p class="mb-1"><strong>Periode:</strong> <?= esc((string) ($demande['date_debut'] ?? '')) ?> -> <?= esc((string) ($demande['date_fin'] ?? '')) ?></p>
        <p class="mb-1"><strong>Duree:</strong> <?= esc((string) ($demande['nb_jours'] ?? 0)) ?> jour(s)</p>
        <p class="mb-3"><strong>Solde disponible:</strong> <?= esc((string) ((int) ($demande['jours_restants'] ?? 0))) ?> jour(s)</p>

        <form method="post" action="<?= $mode === 'reject' ? '/rh/reject/' : '/rh/approve/' ?><?= esc((string) ($demande['id'] ?? 0)) ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Commentaire RH <?= $mode === 'reject' ? '(recommande)' : '(optionnel)' ?></label>
                <textarea class="form-control" name="commentaire_rh" rows="4" placeholder="Ajoutez un commentaire pour l employe"></textarea>
            </div>

            <div class="d-flex gap-2">
                <?php if ($mode === 'reject'): ?>
                    <button class="btn btn-danger" type="submit">Confirmer le refus</button>
                <?php else: ?>
                    <button class="btn btn-success" type="submit">Confirmer l approbation</button>
                <?php endif; ?>
                <a href="/rh" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</body>
</html>
