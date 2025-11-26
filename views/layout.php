<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'MyWeeklyAllowance' ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>MyWeeklyAllowance</h1>
            <nav>
                <?php if (isset($user)): ?>
                    <?php if ($user->role === 'parent'): ?>
                        <a href="/parent/dashboard">Dashboard</a>
                        <a href="/parent/add-teenager">Ajouter Ado</a>
                    <?php else: ?>
                        <a href="/teenager/dashboard">Dashboard</a>
                        <a href="/teenager/history">Historique</a>
                    <?php endif; ?>
                    <a href="/auth/logout">Déconnexion</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container">
        <?= $content ?>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 MyWeeklyAllowance - Projet Hetic TDD</p>
        </div>
    </footer>
</body>
</html>

