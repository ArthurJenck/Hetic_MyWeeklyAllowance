<?php
$title = 'Historique Complet';
$user = App\Middleware\AuthMiddleware::authenticate();
ob_start();
?>

<div class="card">
    <div class="flex-header">
        <h2>Historique Complet</h2>
        <a href="/teenager/dashboard" class="btn btn-sm btn-outline">← Retour</a>
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

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>