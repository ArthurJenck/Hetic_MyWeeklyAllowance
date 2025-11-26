<?php
$title = 'Connexion Parent';
ob_start();
?>

<div class="card">
    <h2>Connexion Parent</h2>

    <?php if (isset($_GET['error'])): ?>
        <?php
        $errorMsg = $_GET['error'] === '1' ? 'Email ou mot de passe incorrect' : \App\Helpers\ErrorMessages::get($_GET['error']);
        ?>
        <div class="alert alert-error"><?= $errorMsg ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['registered'])): ?>
        <div class="alert alert-success">Inscription réussie ! Connectez-vous</div>
    <?php endif; ?>

    <form method="POST" action="/auth/login-parent">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn btn-primary">Se connecter</button>
    </form>

    <p>Pas encore de compte ? <a href="/auth/register">S'inscrire</a></p>
    <p><a href="/auth/login-teenager">Connexion Adolescent</a></p>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>