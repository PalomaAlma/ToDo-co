# ToDoList
========

## Prérequis et installation
  - PHP 7.4
  - MySQL 5.7
  - Composer 2+


Lancer les commandes suivantes après avoir cloné le projet sur votre machine
1. Pour installer les dépendances
```composer install```
2. Pour créer la base de donées (après avoir configurer le fichier .env)
```php/bin console doctrine:database:create```
3. Pour implémenter la base de donnée
```php/bin console make:migration```
puis
```php/bin console doctrine:migrations:migrate```

## Tests
  - Pour lancer les tests via phpunit
```vendor/bin/phpunit```
  - Pour lancer les tests via phpunit avec les test-coverage (vous trouverez le couverture de test dans le dossier public/)
```vendor/bin/phpunit --coverage-html public/test-coverage```

## DataFixtures
  - Pour générer les Data Fixtures
```php/bin console doctrine:fixtures:load```

## Authentification
  - Pour vous connecter en tant qu'administrateur, utilisez les identifiants suivants :
    - username : admin
    - password : 123456
  - Pour vous connecter en tant qu'utilisateur, utilisez les identifiants suivants :
    - username : user
    - password : 123456

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/7aec653f836a4e82a99b85bd7d3e9f90)](https://www.codacy.com/gh/PalomaAlma/ToDo-co/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=PalomaAlma/ToDo-co&amp;utm_campaign=Badge_Grade)
