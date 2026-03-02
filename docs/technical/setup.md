# Environnement local (Docker)
> Les commandes `make` se lancent depuis la machine hote, pas dans le container.

## Prerequis
- Docker + Docker Compose
- Make

## Mode developpement (local)
1. Demarrer le projet:
```bash
make start
```
Cette commande lance `docker compose up -d`, installe les dependances (`composer install`, `npm install`), cree la base si besoin, execute les migrations puis compile les assets en mode dev.

2. Commandes utiles en dev:
- Logs: `make log`
- Shell app: `make connect`
- Tests PHPUnit: `make test`
- Qualite (fixer + phpstan): `make check`
- Watch assets: `make watch`
- Migrations: `make migration` puis `make migrate`

## Mode production (local)
Ne pas modifier `app/.env` pour basculer d'environnement. Utiliser uniquement des overrides locaux non versionnes.

1. Creer ou modifier `app/.env.local`:
```dotenv
APP_ENV=prod
APP_DEBUG=0
```
2. Creer ou modifier `app/.env.prod.local` avec les valeurs locales (DB, secret, mailer, etc.).
3. Regenerer cache + assets:
```bash
docker compose exec app symfony console cache:clear --env=prod --no-debug
docker compose exec app npm run build
```
4. Verifier l'environnement:
```bash
docker compose exec app symfony console about
```
Attendu: `Environment: prod` et `Debug: false`.

## Revenir en mode developpement
1. Remettre `app/.env.local`:
```dotenv
APP_ENV=dev
APP_DEBUG=1
```
2. Recharger cache + assets:
```bash
docker compose exec app symfony console cache:clear --env=dev
docker compose exec app npm run dev
```
3. Verifier:
```bash
docker compose exec app symfony console about
```
Attendu: `Environment: dev` et `Debug: true`.

## Tests: prerequis de configuration
- En environnement `test`, definir `NOREPLY_ADRESS` (exemple: `no-reply@vite-gourmand.test`) dans `app/.env.test` ou `app/.env.test.local`.
- Si cette variable est vide, les scenarios d'inscription peuvent echouer avec `Email \"\" does not comply with addr-spec of RFC 2822`.
