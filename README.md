# Hetic - Projet MyWeeklyAllowance

## Objectif

Vous allez concevoir un module de gestion d’argent de poche pour adolescents, selon la méthode TDD (Test Driven Development).
Votre mission : commencer par les tests unitaires, puis développer le code étape par étape jusqu’à ce que tous les tests passent.

Votre objectif : au moins 85 % de couverture de code.

### Contexte du projet : MyWeeklyAllowance

L’application MyWeeklyAllowance permet aux parents de gérer un “porte-monnaie virtuel” pour leurs ados.
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

   - Modifiez les variables dans `.env` si nécessaire (Configuration BDD, etc.).

### Lancer les tests

Pour exécuter la suite de tests PHPUnit :

```bash
composer test
```

ou

```bash
vendor/bin/phpunit
```

### Lancer le projet en local

Vous pouvez utiliser le serveur interne de PHP pour tester rapidement :

```bash
php -S localhost:8000 -t public
```

Accédez ensuite à `http://localhost:8000`.
