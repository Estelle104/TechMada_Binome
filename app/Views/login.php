<!DOCTYPE html>
<html lang="fr">
<?= view('partials/template_head', ['title' => 'Connexion - TechMada RH']) ?>
<body>
<?php
$flashError = session()->getFlashdata('error');
$flashSuccess = session()->getFlashdata('success');
$oldInput = session()->getFlashdata('_ci_old_input') ?? [];
$emailValue = (string) ($oldInput['email'] ?? '');
?>
<section id="page-login">
    <div class="auth-page geo-bg">
        <div class="auth-split">
            <div class="auth-left">
                <div>
                    <p class="auth-left-brand">TechMada RH<span>Gestion des congés</span></p>
                    <p class="auth-left-text" style="margin-top:2rem">
                        <strong>Bienvenue sur votre espace RH.</strong>
                        Gérez vos demandes de congés, consultez votre solde et suivez l'état de vos demandes en temps réel.
                    </p>
                </div>
                <div class="auth-roles">
                    <div style="font-size:.65rem;text-transform:uppercase;letter-spacing:1px;color:rgba(255,255,255,.25);margin-bottom:4px">Comptes de démonstration</div>
                    <div class="role-pill">
                        <i class="bi bi-shield-check"></i>
                        <div>
                            <div class="role-pill-name">Administrateur</div>
                            <div class="role-pill-cred">admin@test.local · admin123</div>
                        </div>
                    </div>
                    <div class="role-pill">
                        <i class="bi bi-person-check"></i>
                        <div>
                            <div class="role-pill-name">Responsable RH</div>
                            <div class="role-pill-cred">rh@test.local · rh123</div>
                        </div>
                    </div>
                    <div class="role-pill">
                        <i class="bi bi-person"></i>
                        <div>
                            <div class="role-pill-name">Employé</div>
                            <div class="role-pill-cred">employe@test.local · employe123</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="auth-right">
                <p class="auth-title">Connexion</p>
                <p class="auth-sub">Entrez vos identifiants pour accéder à votre espace.</p>

                <?php if ($flashError): ?>
                    <div class="flash flash-error">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <?= esc((string) $flashError) ?>
                    </div>
                <?php endif; ?>
                <?php if ($flashSuccess): ?>
                    <div class="flash flash-success">
                        <i class="bi bi-check-circle-fill"></i>
                        <?= esc((string) $flashSuccess) ?>
                    </div>
                <?php endif; ?>

                <form action="/login" method="post">
                    <?= csrf_field() ?>
                    <div class="f-group">
                        <label class="f-label">Adresse email</label>
                        <input type="email" name="email" class="f-input" placeholder="vous@techmada.mg" value="<?= esc($emailValue) ?>" required />
                    </div>
                    <div class="f-group">
                        <label class="f-label">Mot de passe</label>
                        <input type="password" name="password" class="f-input" placeholder="••••••••" required />
                    </div>
                    <button type="submit" class="btn-primary" style="margin-top:.5rem">
                        Se connecter <i class="bi bi-arrow-right-short"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
</body>
</html>