# Setup

Guide unique pour lancer, verifier et depanner l'environnement local.

> Les commandes `make` se lancent depuis la machine hote, a la racine du repo.

## Prerequis

- Docker + Docker Compose
- Make
- Le reseau Docker externe `traefik_network`
- Sous WSL: Docker Desktop avec l'integration WSL active pour la distribution utilisee

Si le reseau Traefik n'existe pas encore, le creer une seule fois:

```bash
docker network create traefik_network
```

## Demarrage standard

```bash
make start
```

Cette commande:

- demarre les containers
- installe les dependances PHP et Node
- cree la base si besoin
- applique les migrations
- compile les assets en mode dev

## Commandes utiles

- `make boot`: demarre uniquement les containers
- `make connect`: ouvre un shell dans le container `app`
- `make log`: suit les logs Docker
- `make watch`: lance le watch des assets
- `make test`: lance PHPUnit
- `make check`: lance `php-cs-fixer`, `twig-cs-fixer` puis `phpstan`
- `make migration`: genere une migration Doctrine
- `make migrate`: applique les migrations
- `make fixtures-reset`: recree la base et recharge les fixtures
- `make cache-clear`: supprime le cache Symfony
- `make restart`: redemarre la stack puis relance le watch

## Configuration locale

- `.env` a la racine: variables Docker / Traefik
- `app/.env`: base Symfony versionnee
- `app/.env.local` et `app/.env.*.local`: overrides locaux non versionnes

Pour les tests, verifier que ces variables sont renseignees dans `app/.env.test` ou `app/.env.test.local`:

- `NO_REPLY_ADDRESS`
- `OWNER_ADDRESS`

Si elles sont vides, certains scenarios mail peuvent echouer.

## Acces utiles

- Application: host defini par `TRAEFIK_URL_APP` dans `.env`
- MailHog: `http://localhost:8025`

## Debug rapide

Commencer par:

```bash
make log
make cache-clear
make restart
```

Puis verifier l'etat Symfony si besoin:

```bash
docker compose exec app symfony console about
```

## WSL

Si le projet est ouvert via `\\wsl.localhost\\...`, executer les commandes depuis WSL, par exemple:

```bash
wsl bash -lc "cd /home/caim/studi/ECF/vite-gourmand && make test"
```

Si la commande `docker` est introuvable dans WSL, verifier d'abord l'integration Docker Desktop:

1. Docker Desktop > Settings > Resources > WSL Integration
2. Activer la distribution Debian utilisee pour ce projet
3. Redemarrer le terminal WSL avant de relancer `make start`, `make test` ou `make check`
