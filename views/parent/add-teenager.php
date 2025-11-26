<?php
$title = 'Ajouter un Adolescent';
$user = App\Middleware\AuthMiddleware::authenticate();
ob_start();
?>

<div class="card">
    <h2>Ajouter un Adolescent</h2>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'db'): ?>
        <div class="alert alert-error">Une erreur est survenue. Veuillez réessayer.</div>
    <?php endif; ?>

    <form method="POST" action="/parent/add-teenager">
        <div class="form-group">
            <label for="name">Nom</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="birth_date">Date de naissance</label>
            <input type="date" id="birth_date" name="birth_date" required>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="weekly_allowance">Allocation hebdomadaire (€)</label>
            <input type="number" step="0.01" id="weekly_allowance" name="weekly_allowance" value="0" required>
        </div>

        <button type="submit" class="btn btn-primary">Ajouter</button>
        <a href="/parent/dashboard" class="btn">Annuler</a>
    </form>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>