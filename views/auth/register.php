<?php
$title = 'Inscription';
ob_start();
?>

<div class="card">
    <h2>Inscription Parent</h2>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'db'): ?>
        <div class="alert alert-error">Une erreur est survenue. Veuillez réessayer.</div>
    <?php endif; ?>

    <form method="POST" action="/auth/register">
        <div class="form-group">
            <label for="name">Nom</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
        </div>

        <input type="hidden" name="role" value="parent">

        <button type="submit" class="btn btn-primary">S'inscrire</button>
    </form>

    <p>Déjà un compte ? <a href="/auth/login-parent">Se connecter</a></p>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>