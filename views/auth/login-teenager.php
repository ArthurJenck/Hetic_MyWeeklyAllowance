<?php
$title = 'Connexion Adolescent';
ob_start();
?>

<div class="card" style="max-width: 480px; margin: 2rem auto;">
    <h2>Connexion Adolescent</h2>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error">Email ou mot de passe incorrect</div>
    <?php endif; ?>

    <form method="POST" action="/auth/login-teenager">
        <div class="form-group">
            <label for="email">Votre Email</label>
            <input type="email" id="email" name="email" placeholder="ado@example.com" required>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%;">Se connecter</button>
    </form>

    <p class="text-center text-muted" style="margin-top: 1.5rem;">
        <a href="/auth/login-parent" style="color: hsl(var(--foreground-muted));">Connexion Parent</a>
    </p>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>