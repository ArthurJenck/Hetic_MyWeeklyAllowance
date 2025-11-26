<?php
$title = 'Dépôt sur Wallet Commun';
$user = App\Middleware\AuthMiddleware::authenticate();
ob_start();
?>

<div class="card">
    <h2>Déposer de l'Argent</h2>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error"><?= \App\Helpers\ErrorMessages::get($_GET['error']) ?></div>
    <?php endif; ?>

    <p>Ajoutez des fonds à votre wallet commun pour les redistribuer à vos adolescents.</p>
</div>

<div class="card">
    <h3>Montant du Dépôt</h3>
    <form method="POST" action="/parent/deposit">
        <div class="form-group">
            <label for="amount">Montant (€) *</label>
            <input type="number" step="0.01" id="amount" name="amount" min="0.01" required>
        </div>

        <div class="card" style="background: #FFF9E6; border: 1px solid #FFD700; margin: 1.5rem 0;">
            <h4>Simulation Paiement par Carte Bancaire</h4>
            <p style="font-size: 0.9rem; color: #856404; margin-bottom: 1rem;">
                <strong>Note :</strong> Cette section est purement informative. Aucune carte n'est débitée. Seul le montant ci-dessus sera ajouté à votre wallet.
            </p>

            <div class="form-group">
                <label for="card_number">Numéro de carte</label>
                <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="card_expiry">Date d'expiration</label>
                    <input type="text" id="card_expiry" name="card_expiry" placeholder="MM/AA" maxlength="5">
                </div>
                <div class="form-group">
                    <label for="card_cvv">CVV</label>
                    <input type="text" id="card_cvv" name="card_cvv" placeholder="123" maxlength="3">
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn btn-primary">Déposer</button>
            <a href="/parent/dashboard" class="btn">Annuler</a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>