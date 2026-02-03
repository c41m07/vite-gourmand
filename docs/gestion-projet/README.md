# Lancer avec Docker
> Les commandes Make fonctionnent uniquement en dehors du container.

- Démarrer : `make boot`
- Entrer dans le container : `make connect`
  - Quitter le container : `exit`
- Logs : `make log`
- Fixers (PHP/Twig) : `make fixer`
- PHPStan : `make phpstan`
- `make check` exécute fixer + phpstan
