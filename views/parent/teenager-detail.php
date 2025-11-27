<?php
$title = 'Détails Adolescent';
$user = App\Middleware\AuthMiddleware::authenticate();
ob_start();
?>

<div class="card">
    <h2><?= htmlspecialchars($teenager['name']) ?></h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Opération réussie !</div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error"><?= \App\Helpers\ErrorMessages::get($_GET['error']) ?></div>
    <?php endif; ?>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Solde Total</div>
            <div class="stat-value"><?= number_format($wallet['balance'], 2) ?> €</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Allocation Hebdo</div>
            <div class="stat-value"><?= number_format($wallet['weekly_allowance'], 2) ?> €</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Reste Hebdo</div>
            <div class="stat-value" style="color: hsl(var(--primary));"><?= number_format($wallet['weekly_remaining'], 2) ?> €</div>
        </div>
    </div>
</div>

<div class="card">
    <h3>Définir Allocation</h3>
    <form method="POST" action="/parent/set-allowance">
        <input type="hidden" name="teenager_id" value="<?= $teenager['id'] ?>">
        <div class="form-group">
            <label for="amount">Montant hebdomadaire (€)</label>
            <input type="number" step="0.01" id="amount" name="amount" value="<?= $wallet['weekly_allowance'] ?>" placeholder="20.00" required>
            <small>Montant que l'adolescent pourra dépenser chaque semaine</small>
        </div>
        <button type="submit" class="btn btn-primary">Mettre à jour l'allocation</button>
    </form>
</div>

<div class="card">
    <h3>Transférer de l'Argent</h3>
    <form method="POST" action="/parent/transfer-money">
        <input type="hidden" name="teenager_id" value="<?= $teenager['id'] ?>">
        <div class="form-group">
            <label for="amount">Montant (€)</label>
            <input type="number" step="0.01" id="amount" name="amount" placeholder="10.00" required>
            <small>L'argent sera transféré depuis votre wallet commun</small>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <input type="text" id="description" name="description" value="Transfert du parent" readonly>
        </div>
        <button type="submit" class="btn btn-primary">Transférer</button>
    </form>
</div>

<div class="card">
    <h3>Historique des Transactions</h3>
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
                <?php foreach ($transactions as $transaction): ?>
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

<div class="card">
    <h3>Zone Dangereuse</h3>
    <p class="text-muted text-sm mb-2">La suppression de l'adolescent est irréversible.</p>
    <form method="POST" action="/parent/delete-teenager" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet adolescent ?');">
        <input type="hidden" name="teenager_id" value="<?= $teenager['id'] ?>">
        <button type="submit" class="btn btn-danger">Supprimer l'Adolescent</button>
    </form>
</div>

<a href="/parent/dashboard" class="btn btn-outline">← Retour au Dashboard</a>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>