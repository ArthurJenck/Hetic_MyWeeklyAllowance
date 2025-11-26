<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyWeeklyAllowance - Gérez l'argent de poche de vos adolescents</title>
    <link rel="stylesheet" href="/css/style.css">
</head>

<body>
    <header>
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h1>MyWeeklyAllowance</h1>
                <nav>
                    <a href="/auth/login-parent">Connexion Parent</a>
                    <a href="/auth/login-teenager">Connexion Ado</a>
                    <a href="/auth/register" class="btn btn-primary btn-sm">S'inscrire</a>
                </nav>
            </div>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container">
                <h2 class="hero-title">Gérez l'argent de poche de vos adolescents en toute simplicité</h2>
                <p class="hero-subtitle">Une plateforme intuitive pour apprendre la gestion financière à vos enfants</p>
                <div class="hero-cta">
                    <a href="/auth/register" class="cta-btn">Commencer gratuitement</a>
                    <a href="/auth/login-parent" class="btn">Se connecter</a>
                </div>
            </div>
        </section>

        <section class="how-it-works">
            <div class="container">
                <h2 style="text-align: center; margin-bottom: 3rem;">Comment ça marche ?</h2>
                <div class="steps">
                    <div class="step">
                        <div class="step-number">1</div>
                        <h3>Créez votre compte parent</h3>
                        <p>Inscrivez-vous gratuitement et ajoutez vos adolescents à votre espace familial.</p>
                    </div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <h3>Définissez les allocations</h3>
                        <p>Fixez une allocation hebdomadaire pour chaque adolescent et alimentez votre wallet commun.</p>
                    </div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <h3>Suivez les dépenses</h3>
                        <p>Vos adolescents enregistrent leurs dépenses et vous gardez un œil sur leur budget.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="features">
            <div class="container">
                <h2 style="text-align: center; margin-bottom: 3rem;">Fonctionnalités</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <h3>💰 Pour les Parents</h3>
                        <ul>
                            <li>Wallet commun pour gérer les fonds</li>
                            <li>Définir des allocations hebdomadaires</li>
                            <li>Transférer de l'argent instantanément</li>
                            <li>Suivre les dépenses en temps réel</li>
                            <li>Gérer plusieurs adolescents</li>
                        </ul>
                    </div>
                    <div class="feature-card">
                        <h3>🎯 Pour les Adolescents</h3>
                        <ul>
                            <li>Visualiser le solde disponible</li>
                            <li>Suivre l'allocation hebdomadaire</li>
                            <li>Enregistrer les dépenses</li>
                            <li>Consulter l'historique complet</li>
                            <li>Apprendre à gérer un budget</li>
                        </ul>
                    </div>
                    <div class="feature-card">
                        <h3>📊 Transparence Totale</h3>
                        <ul>
                            <li>Historique des transactions</li>
                            <li>Limites hebdomadaires automatiques</li>
                            <li>Notifications de dépenses</li>
                            <li>Rapport détaillé</li>
                            <li>Interface simple et claire</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta-section">
            <div class="container" style="text-align: center;">
                <h2>Prêt à commencer ?</h2>
                <p style="font-size: 1.2rem; margin: 1rem 0 2rem;">Rejoignez MyWeeklyAllowance aujourd'hui et enseignez la gestion financière à vos adolescents.</p>
                <a href="/auth/register" class="cta-btn">Créer un compte gratuit</a>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 MyWeeklyAllowance - Projet Hetic TDD</p>
            <p style="margin-top: 0.5rem;">
                <a href="/auth/login-parent" style="color: inherit; margin: 0 1rem;">Connexion Parent</a>
                <a href="/auth/login-teenager" style="color: inherit; margin: 0 1rem;">Connexion Ado</a>
                <a href="/auth/register" style="color: inherit; margin: 0 1rem;">S'inscrire</a>
            </p>
        </div>
    </footer>
</body>

</html>