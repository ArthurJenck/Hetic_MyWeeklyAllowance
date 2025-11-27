# Hetic - Projet My Weekly Allowance

## Objectif

Vous allez concevoir un module de gestion d’argent de poche pour adolescents, selon la méthode TDD (Test Driven Development).
Votre mission : commencer par les tests unitaires, puis développer le code étape par étape jusqu’à ce que tous les tests passent.

Votre objectif : au moins 85 % de couverture de code.

### Contexte du projet : My Weekly Allowance

L’application My Weekly Allowance permet aux parents de gérer un “porte-monnaie virtuel” pour leurs ados.
Chaque adolescent a un compte d’argent de poche, et chaque parent peut :

- créer un compte pour un ado,
- déposer de l’argent,
- enregistrer des dépenses,
- fixer une allocation hebdomadaire automatique.

### Organisation

- Phase 1 – Rédaction des tests unitaires (RED)
- Phase 2 – Implémentation du code (GREEN)
- Phase 3 – Refactoring
- Phase 4 – Vérification de la couverture

## Infos pratiques

- **Site en ligne :** [https://my-weekly-allowance.arthurjenck.com](https://my-weekly-allowance.arthurjenck.com)
- Dans la continuité de la démarche TDD, une branche tests-only contient le projet avec les tests uniquement, avant d'avoir été merge sur main.
- Auteur : **Arthur JENCK**

## Installation et Configuration

### Prérequis

- PHP 8.2 ou supérieur
- Composer

### Installation

1. Clonez le dépôt.
2. Installez les dépendances :

   ```bash
   composer install
   ```

3. Configurez l'environnement :
   - Copiez le fichier `env.example` vers `.env` :

     ```bash
     cp .env.example .env
     ```

   - Modifiez les variables dans `.env` (Configuration BDD, etc.).
4. Importez le schéma de base de données situé dans `database/schema.sql`.

### Lancer les tests

Pour exécuter la suite de tests PHPUnit :

```bash
composer test
```

ou

```bash
vendor/bin/phpunit
```

### Couverture de code

Pour générer le rapport de couverture :

```bash
composer test --coverage-text
```

Résultat actuel :

```text
PHPUnit 10.5.58 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.4.15 with Xdebug 3.4.7

OK (167 tests, 322 assertions)

Code Coverage Report:
  2025-11-27 11:50:43

 Summary:
  Classes: 47.37% (9/19)
  Methods: 81.82% (99/121)
  Lines:   90.10% (464/515)
```

### Lancer le projet en local

Puisque l'architecture a été adaptée pour supporter les hébergements mutualisés (index.php à la racine) :

```bash
php -S localhost:8000
```

Accédez ensuite à `http://localhost:8000`.

## Déploiement (Hébergement Mutualisé / FTP)

Le projet est configuré pour fonctionner à la racine du dossier `htdocs` ou `www`.

1. Transférez tous les fichiers à la racine de votre espace FTP (sauf `.git`).
2. **Important :** Transférez le dossier `vendor` complet (car `composer install` n'est souvent pas disponible).
3. Créez un fichier `.env` à la racine avec vos identifiants de base de données de production (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`).
4. Importez `database/schema.sql` dans votre base de données via PHPMyAdmin.
5. Assurez-vous que le fichier `.htaccess` est bien pris en compte pour la réécriture d'URL.
