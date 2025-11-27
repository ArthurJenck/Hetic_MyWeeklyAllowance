<?php
$title = 'Inscription';
ob_start();
?>

<div class="card" style="max-width: 480px; margin: 2rem auto;">
    <h2>Inscription Parent</h2>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'db'): ?>
        <div class="alert alert-error">Une erreur est survenue. Veuillez réessayer.</div>
    <?php endif; ?>

    <form method="POST" action="/auth/register">
        <div class="form-group">
            <label for="name">Nom</label>
            <input type="text" id="name" name="name" placeholder="Jean Dupont" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="parent@example.com" required>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required>
            <small>Minimum 8 caractères recommandés</small>
        </div>

        <input type="hidden" name="role" value="parent">

        <button type="submit" class="btn btn-primary" style="width: 100%;">S'inscrire</button>
    </form>

    <p class="text-center text-muted" style="margin-top: 1.5rem;">
        Déjà un compte ? <a href="/auth/login-parent" style="color: hsl(var(--primary));">Se connecter</a>
    </p>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>