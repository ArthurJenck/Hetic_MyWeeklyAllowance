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

    <div class="stats-grid" style="grid-template-columns: 1fr 1fr; margin-top: 1.5rem;">
        <div class="stat-card">
            <div class="stat-label">Allocation Hebdo</div>
            <div class="stat-value"><?= number_format($wallet['weekly_allowance'], 2) ?> €</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Reste Cette Semaine</div>
            <div class="stat-value" style="color: hsl(var(--primary));"><?= number_format($wallet['weekly_remaining'], 2) ?> €</div>
        </div>
    </div>
</div>

<div class="card">
    <h3>Enregistrer une Dépense</h3>
    <form method="POST" action="/teenager/expense">
        <div class="form-group">
            <label for="amount">Montant (€)</label>
            <input type="number" step="0.01" id="amount" name="amount" placeholder="10.50" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <input type="text" id="description" name="description" placeholder="Ex: Cinéma, Snacks..." required>
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer la dépense</button>
    </form>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 style="margin-bottom: 0;">Mes Dernières Transactions</h3>
        <?php if (!empty($transactions)): ?>
            <a href="/teenager/history" class="btn btn-sm btn-outline">Voir tout</a>
        <?php endif; ?>
    </div>
    <?php if (empty($transactions)): ?>
        <p class="text-muted">Aucune transaction pour le moment.</p>
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
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>