<?php
$title = 'Dashboard Parent';
$user = App\Middleware\AuthMiddleware::authenticate();
ob_start();
?>

<div class="card">
    <h2>Bienvenue, <?= htmlspecialchars($parentData['name']) ?> !</h2>

    <?php if (isset($_GET['registered'])): ?>
        <div class="alert alert-success">Inscription réussie ! Bienvenue sur MyWeeklyAllowance.</div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <?php
        $successMessages = [
            'teenager_added' => 'Adolescent ajouté avec succès !',
            'teenager_deleted' => 'Adolescent supprimé.',
            'deposit_made' => 'Dépôt effectué avec succès !'
        ];
        $successMsg = $successMessages[$_GET['success']] ?? 'Opération réussie !';
        ?>
        <div class="alert alert-success"><?= $successMsg ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['warning'])): ?>
        <?php
        $warningMessages = [
            'insufficient_funds_for_initial' => 'Adolescent créé, mais montant initial non transféré (solde insuffisant).'
        ];
        $warningMsg = $warningMessages[$_GET['warning']] ?? 'Attention';
        ?>
        <div class="alert" style="background-color: #FFF3CD; color: #856404; border-left: 4px solid #FFC107;"><?= $warningMsg ?></div>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Mon Wallet Commun</h3>
    <div class="wallet-info">
        <p class="balance"><strong>Solde:</strong> <?= $wallet ? number_format($wallet['balance'], 2) : '0.00' ?> €</p>
    </div>
    <a href="/parent/deposit" class="btn btn-primary">Déposer de l'Argent</a>
</div>

<div class="card">
    <h3>Mes Adolescents</h3>

    <?php if (empty($teenagers)): ?>
        <p>Aucun adolescent ajouté pour le moment.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Solde Total</th>
                    <th>Allocation Hebdo</th>
                    <th>Reste Hebdo</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teenagers as $teenager): ?>
                    <tr>
                        <td><?= htmlspecialchars($teenager['name']) ?></td>
                        <td><?= $teenager['wallet'] ? number_format($teenager['wallet']['balance'], 2) : '0.00' ?> €</td>
                        <td><?= $teenager['wallet'] ? number_format($teenager['wallet']['weekly_allowance'], 2) : '0.00' ?> €</td>
                        <td><?= $teenager['wallet'] ? number_format($teenager['wallet']['weekly_remaining'], 2) : '0.00' ?> €</td>
                        <td>
                            <a href="/parent/teenager?id=<?= $teenager['id'] ?>" class="btn btn-sm btn-primary">Gérer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="/parent/add-teenager" class="btn btn-primary">Ajouter un Adolescent</a>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>