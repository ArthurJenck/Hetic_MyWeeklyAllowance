<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'My Weekly Allowance' ?></title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="stylesheet" href="/css/style.css">
</head>

<body>
    <header>
        <div class="container">
            <div class="header-content">
                <h1>
                    <?php if (isset($user)): ?>
                        <?php if ($user->role === 'parent'): ?>
                            <a href="/parent/dashboard">My Weekly Allowance</a>
                        <?php else: ?>
                            <a href="/teenager/dashboard">My Weekly Allowance</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="/">My Weekly Allowance</a>
                    <?php endif; ?>
                </h1>
                <input type="checkbox" id="menu-toggle" class="menu-toggle">
                <label for="menu-toggle" class="burger-icon">
                    <span></span>
                    <span></span>
                    <span></span>
                </label>
                <nav>
                    <?php if (isset($user)): ?>
                        <?php if ($user->role === 'parent'): ?>
                            <a href="/parent/dashboard">Dashboard</a>
                            <a href="/parent/add-teenager">Ajouter Ado</a>
                            <a href="/auth/logout" class="btn-ghost">Déconnexion</a>
                        <?php else: ?>
                            <a href="/teenager/dashboard">Dashboard</a>
                            <a href="/teenager/history">Historique</a>
                            <a href="/auth/logout" class="btn-ghost">Déconnexion</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>

    <main class="container">
        <?= $content ?>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 My Weekly Allowance - Projet Hetic TDD</p>
        </div>
    </footer>
</body>

</html>