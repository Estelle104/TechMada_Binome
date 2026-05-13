<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Employe - Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-3">
  <div class="container">
    <h1>Tableau de bord - Employé</h1>
    <p>Bienvenue, <?= esc(session()->get('user')['email'] ?? 'utilisateur') ?></p>
    <p><a href="/logout" class="btn btn-outline-secondary">Se déconnecter</a></p>
  </div>
</body>
</html>