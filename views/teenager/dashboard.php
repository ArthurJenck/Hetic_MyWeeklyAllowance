<?php
$title = 'Dashboard Adolescent';
$user = App\Middleware\AuthMiddleware::authenticate();
ob_start();
?>

<div class="card">
    <h2>Mon Espace</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Dépense enregistrée !</div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error"><?= \App\Helpers\ErrorMessages::get($_GET['error']) ?></div>
    <?php endif; ?>

    <div class="wallet-info">
        <p><strong>Allocation Hebdo:</strong> <?= number_format($wallet['weekly_allowance'], 2) ?> €</p>
        <p><strong>Reste Cette Semaine:</strong> <?= number_format($wallet['weekly_remaining'], 2) ?> €</p>
    </div>
</div>

<div class="card">
    <h3>Enregistrer une Dépense</h3>
    <form method="POST" action="/teenager/expense">
        <div class="form-group">
            <label for="amount">Montant (€)</label>
            <input type="number" step="0.01" id="amount" name="amount" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <input type="text" id="description" name="description" placeholder="Ex: Cinéma, Snacks..." required>
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </form>
</div>

<div class="card">
    <h3>Mes Dernières Transactions</h3>
    <?php if (empty($transactions)): ?>
        <p>Aucune transaction pour le moment.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Montant</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($transactions, 0, 10) as $transaction): ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($transaction['created_at'])) ?></td>
                        <td><?= htmlspecialchars($transaction['type']) ?></td>
                        <td><?= number_format($transaction['amount'], 2) ?> €</td>
                        <td><?= htmlspecialchars($transaction['description']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="/teenager/history" class="btn">Voir tout l'historique</a>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>