<?php
$title = 'Ajouter un Adolescent';
$user = App\Middleware\AuthMiddleware::authenticate();
ob_start();
?>

<div class="card" style="max-width: 640px; margin: 2rem auto;">
    <h2>Ajouter un Adolescent</h2>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'db'): ?>
        <div class="alert alert-error">Une erreur est survenue. Veuillez réessayer.</div>
    <?php endif; ?>

    <form method="POST" action="/parent/add-teenager">
        <div class="form-group">
            <label for="name">Nom</label>
            <input type="text" id="name" name="name" placeholder="Marie Dupont" required>
        </div>

        <div class="form-group">
            <label for="birth_date">Date de naissance</label>
            <input type="date" id="birth_date" name="birth_date" required>
        </div>

        <div class="form-group">
            <label for="email">Email de l'adolescent</label>
            <input type="email" id="email" name="email" placeholder="marie@example.com" required>
            <small>Cet email servira pour la connexion de l'adolescent</small>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required>
            <small>Mot de passe pour le compte de l'adolescent</small>
        </div>

        <div class="form-group">
            <label for="weekly_allowance">Allocation hebdomadaire (€)</label>
            <input type="number" step="0.01" id="weekly_allowance" name="weekly_allowance" value="0" placeholder="20.00" required>
            <small>Montant que l'adolescent pourra dépenser chaque semaine</small>
        </div>

        <div class="form-group">
            <label for="initial_amount">Montant initial à transférer (€) - Optionnel</label>
            <input type="number" step="0.01" id="initial_amount" name="initial_amount" value="0" min="0" placeholder="0.00">
            <small>L'argent sera transféré depuis votre wallet commun</small>
        </div>

        <div class="btn-group" style="margin-top: 1rem;">
            <button type="submit" class="btn btn-primary">Ajouter l'adolescent</button>
            <a href="/parent/dashboard" class="btn btn-outline">Annuler</a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>