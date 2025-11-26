<?php
$title = 'Connexion Adolescent';
ob_start();
?>

<div class="card">
    <h2>Connexion Adolescent</h2>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error">Email du parent ou mot de passe incorrect</div>
    <?php endif; ?>

    <form method="POST" action="/auth/login-teenager">
        <div class="form-group">
            <label for="parent_email">Email du Parent</label>
            <input type="email" id="parent_email" name="parent_email" required>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn btn-primary">Se connecter</button>
    </form>

    <p><a href="/auth/login-parent">Connexion Parent</a></p>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>