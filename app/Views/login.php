<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - TechMada RH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #1c2b1e; min-height: 100vh; display: grid; place-items: center; }
        .card-login { width: 100%; max-width: 430px; border-radius: 14px; }
    </style>
</head>
<body>
    <div class="card card-login shadow">
        <div class="card-body p-4">
            <h1 class="h4 mb-2">Connexion</h1>
            <p class="text-muted mb-3">Espace gestion des conges - Responsable RH</p>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger py-2"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success py-2"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>

            <form action="/login" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="rh@test.local" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mot de passe</label>
                    <input type="password" name="password" class="form-control" placeholder="rh123" required>
                </div>
                <button class="btn btn-success w-100" type="submit">Se connecter</button>
            </form>

            <hr>
            <div class="small text-muted">Compte test RH: <strong>rh@test.local</strong> / <strong>rh123</strong></div>
        </div>
    </div>
</body>
</html>