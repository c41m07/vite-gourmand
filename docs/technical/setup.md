# Environnement de developpement (Docker)
> Les commandes `make` se lancent depuis la machine hote, pas dans le container.

## Prerequis
- Docker + Docker Compose
- Make

## Demarrage rapide
- Demarrer les containers : `make boot`
- Entrer dans le container app : `make connect`
- Initialiser deps + assets : `make start` (composer install, npm install, migrations, build)

## Commandes utiles
- Logs : `make log`
- Qualite de code : `make check`
- Fixers (PHP/Twig) : `make fixer`
- PHPStan : `make phpstan`
- Assets en watch : `make watch`
- Migrations : `make migration` puis `make migrate`
