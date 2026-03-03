# AGENTS.md

## 1) Contexte du projet

### Stack detectee
- Framework: Symfony 8 (`symfony/framework-bundle: 8.0.*`).
- Langage: PHP `>=8.4`.
- BDD: Doctrine ORM + Doctrine Migrations.
- Front: Twig + Webpack Encore + Stimulus + Bootstrap.
- Outillage local: Docker Compose + Make.

### Dossiers cles
- `app/src` (Controller, Entity, Form, Repository).
- `app/config` (packages Symfony, Doctrine, Security, Monolog).
- `app/templates`, `app/public`, `app/tests`, `app/migrations`, `app/var`.
- `docker/`, `docker-compose.yaml`, `Makefile`, `docs/`, `sql/`.

### Conventions majeures observees
- `tests/` est configure comme unique testsuite PHPUnit.
- Aucun pipeline CI detecte (`.github/workflows` et `.gitlab-ci.yml` absents).
- Les commandes de reference sont centralisees dans le `Makefile` et `docs/technical/setup.md`.

## 2) Setup (copier/coller)

### Prerequis
- Docker + Docker Compose
- Make

### Setup recommande (prouve)
```bash
make start
```

`make start` enchaine `boot` puis `dev-dependencies`, ce qui execute:
- `docker compose up -d`
- `composer install`
- `npm install`
- `symfony console d:d:c --if-not-exists`
- `symfony console d:m:m --no-interaction`
- `npm run dev`

### Environnement
- Fichiers detectes: `.env` (racine), `app/.env`, `app/.env.dev`, `app/.env.test`.
- `.env.example` / `.env.dist` non detectes.
- Regle stricte: ne jamais ajouter de secrets en clair dans des fichiers versionnes; privilegier les surcharges locales (`.env.local`, `.env.*.local`).

### Base de donnees
- Creation DB: `symfony console d:d:c --if-not-exists` (via `make start`).
- Migration DB: `symfony console d:m:m --no-interaction` (via `make start` / `make migrate`).
- Import SQL (`sql/schema.sql`, `sql/seed.sql`): **Optionnel / a confirmer**.
  Ce qui manque pour valider: procedure documentee d'import dans `docs/` ou cible explicite dans le `Makefile`.

## 3) Lancer en local

### Commande principale
```bash
make start
```

### Alternatives prouvees
```bash
make boot
make connect
make log
make watch
```

### Notes d'usage
- Les commandes `make` sont prevues depuis l'hote (pas depuis le container).
- Aucune commande `symfony server:start` ou `php -S ...` n'est prouvee dans le repo.

## 4) Tests & Qualite - Strategie de tests

### Structure des tests detectee
- Suite unique PHPUnit: `Project Test Suite` sur le dossier `tests`.
- Les tests presents sont principalement des tests fonctionnels web (`WebTestCase`) et API controller.
- Aucune separation explicite `unit/functional/integration/e2e` en sous-dossiers dedies.

### Commandes de tests et qualite (prouvees)
```bash
make test
make phpstan
make fixer
make check
```

- `make test`: `./vendor/bin/phpunit --testdox`.
- `make phpstan`: `./vendor/bin/phpstan analyse -c phpstan.dist.neon`.
- `make fixer`: PHP CS Fixer + Twig CS Fixer (commande destructive).
- `make check`: enchaine `fixer` puis `phpstan`.

### Commande tout-en-un
- `make check` (existante et prouvee).

## 5) Debug & Logs

### Logs
- Flux applicatif dev/test: `"%kernel.logs_dir%/%kernel.environment%.log"` (Monolog).
- En prod, sortie principale sur `stderr` (Monolog `php://stderr`).

### Debug/profiler
- Environnement par defaut: `APP_ENV=dev` (`app/.env`).
- Web Profiler active en dev (toolbar + routes `/_wdt` et `/_profiler`).

### Commandes utiles (prouvees)
```bash
make log
make cache-clear
make restart
```

### Mini check-list "quand ca plante"
1. Verifier l'etat et les erreurs runtime avec `make log`.
2. Vider le cache projet via `make cache-clear`, puis relancer (`make restart` ou `make start`).
3. Verifier que l'environnement attendu est charge (`APP_ENV` dans `app/.env`, `APP_ENV=test` via `phpunit.dist.xml`).
4. En dev, inspecter `/_profiler` pour les exceptions/requetes SQL.

## 6) Conventions de code

- Style: regles PER-CS/Symfony definies dans `app/.php-cs-fixer.dist.php`.
- `declare(strict_types=1);` non generalise dans `app/src` (ne pas imposer en masse sans decision d'equipe).
- Architecture observee:
  - Controllers pour HTTP/routing.
  - Form classes dans `app/src/Form`.
  - Acces donnees via repositories Doctrine.
- Changements attendus: minimalistes, cibles, sans refactor transverse hors besoin explicite.

## 7) Securite (STRICT)

### Interdits
- Commiter des secrets (token, mot de passe, cle API).
- Exposer des donnees sensibles dans logs/dumps/captures.
- Introduire un bypass d'authentification, roles ou CSRF.

### Regles
- Toute modification de `app/config/packages/security.yaml` doit inclure:
  - impact securite explicite;
  - tests associes (auth, roles, acces).
- Si un fichier d'env versionne contient des credentials, le traiter comme incident a corriger, sans re-exposer les valeurs.
- Ne jamais proposer de contournement de securite "temporaire".

## 8) Demander confirmation avant...

- Toute migration DB ou changement de schema.
- Import SQL massif (`sql/schema.sql`, `sql/seed.sql`).
- Suppression/renommage massif de fichiers.
- Refactor transverse.
- Upgrade majeur de dependances (Symfony, Doctrine, PHP, etc.).
- Modifications infra (Docker, Makefile, reseau, CI/CD).

## 9) Format standard pour les livraisons (comme GPT)

Toute proposition de changement/PR doit suivre:
- A) Contexte
- B) Decisions
- C) Changements
- D) Fichiers impactes
- E) Verifications (commandes)
- F) Risques & rollback

## 10) Appendix - SOURCES (preuve obligatoire)

| Commande | Source | Emplacement |
|---|---|---|
| `make start` | `Makefile` | cible `start` (l.75) |
| `make boot` | `Makefile` | cible `boot` (l.69-70) |
| `make connect` | `Makefile` | cible `connect` (l.72-73) |
| `make log` | `Makefile` | cible `log` (l.82-83) |
| `make watch` | `Makefile` | cible `watch` (l.31-32) |
| `make test` | `Makefile` | cible `test` (l.85-86) |
| `make phpstan` | `Makefile` | cible `phpstan` (l.11-12) |
| `make fixer` | `Makefile` | cible `fixer` (l.17-19) |
| `make check` | `Makefile` | cible `check` (l.9) |
| `make migration` | `Makefile` | cible `migration` (l.51-52) |
| `make migrate` | `Makefile` | cible `migrate` (l.48-49) |
| `make cache-clear` | `Makefile` | cible `cache-clear` (l.57-58) |
| `make restart` | `Makefile` | cible `restart` (l.80) |
| `make check-security` | `Makefile` | cible `check-security` (l.54-55) |
| `docker compose up -d` | `Makefile` | expansion `DOCKER_COMPOSE` + cible `boot` (l.1, l.69-70) |
| `composer install` | `Makefile` | cible `dev-dependencies` (l.24-25) |
| `npm install` | `Makefile` | cible `dev-dependencies` (l.24-26) |
| `symfony console d:d:c --if-not-exists` | `Makefile` | cible `dev-dependencies` (l.27) |
| `symfony console d:m:m --no-interaction` | `Makefile` | cibles `dev-dependencies` et `migrate` (l.28, l.49) |
| `npm run dev` | `Makefile` | cible `dev-dependencies` (l.29) |
| `npm run watch` | `Makefile` | cible `watch` (l.32) |
| `./vendor/bin/phpunit --testdox` | `Makefile` | cible `test` (l.86) |
| `./vendor/bin/phpstan analyse -c phpstan.dist.neon` | `Makefile` | cible `phpstan` (l.12) |
| `./vendor/bin/php-cs-fixer fix --show-progress="dots" -v` | `Makefile` | cible `fixer` (l.18) |
| `./vendor/bin/twig-cs-fixer lint --fix --no-cache --config=.twig-cs-fixer.dist.php` | `Makefile` | cible `fixer` (l.19) |
| `./vendor/bin/twigcs templates/` | `Makefile` | cible `twigcs` (l.21-22) |
| `make boot` | `docs/technical/setup.md` | "Demarrage rapide" (l.9) |
| `make connect` | `docs/technical/setup.md` | "Demarrage rapide" (l.10) |
| `make start` | `docs/technical/setup.md` | "Demarrage rapide" (l.11) |
| `make log` | `docs/technical/setup.md` | "Commandes utiles" (l.14) |
| `make check` | `docs/technical/setup.md` | "Commandes utiles" (l.15) |
| `make fixer` | `docs/technical/setup.md` | "Commandes utiles" (l.16) |
| `make phpstan` | `docs/technical/setup.md` | "Commandes utiles" (l.17) |
| `make watch` | `docs/technical/setup.md` | "Commandes utiles" (l.18) |
| `make migration` puis `make migrate` | `docs/technical/setup.md` | "Commandes utiles" (l.19) |
| `encore dev` (`npm run dev`) | `app/package.json` | `scripts.dev` (l.19) |
| `encore dev --watch` (`npm run watch`) | `app/package.json` | `scripts.watch` (l.20) |

## 11) Checklist obligatoire de fin d'action

Cette check-list est a executer a la fin de chaque intervention, avant livraison:

1. Tests:
   - Se placer a la racine du repo avant test.
   - Lancer `make test`.
   - Si echec: corriger la cause racine, puis relancer jusqu'a resultat vert.

2. Controle encodage / caracteres:
   - Verifier l'absence de sequences mojibake visibles et non visibles (ex: `U+00C3`, `U+00C2`, `U+00E2`).
   - Corriger les chaines affectees dans les fichiers modifies (UI, messages, templates, etc.).

3. Verification UTF-8:
   - Verifier que les fichiers modifies sont en UTF-8 sans BOM.
   - Si un BOM ou un encodage incorrect est detecte, le corriger avant livraison.

4. Orthographe et grammaire:
   - Relire les textes visibles utilisateur modifies.
   - Corriger les fautes d'orthographe, de grammaire et de ponctuation.

5. Revalidation finale:
   - Relancer `make test` apres corrections textuelles/encodage.
   - Ne livrer que si la suite est verte.

## 12) Execution shell et environnement

- Quand le repo est sous `\\wsl.localhost\\...`, executer les commandes projet via WSL:
  - format recommande: `wsl bash -lc "cd /home/.../vite-gourmand && <commande>"`.
- Verifier la disponibilite des outils avant usage (`git`, `make`, `rg`).
- Si `rg` est indisponible, utiliser un fallback (`grep`/`Select-String`) avec perimetre explicite.
- `make test` doit etre lance depuis la racine du repository (pas depuis `app/`).
- En cas d'erreur de quoting/escaping, preferer une commande plus simple et relancable plutot qu'un one-liner fragile.

## 13) Politique encodage, EOL et corrections texte

- Tous les fichiers modifies doivent etre en UTF-8 sans BOM.
- Les fins de ligne cibles sont `LF` (eviter les diffs parasites `CRLF/LF`).
- Controle obligatoire des caracteres:
  - visibles: sequences mojibake (`U+00C3`, `U+00C2`, `U+00E2`, etc.).
  - non visibles: `ZWSP`, `ZWNJ`, `ZWJ`, `BOM`, `NBSP`, caracteres bidi.
- Pour la documentation, ne pas inclure des exemples mojibake litteraux; preferer la notation par codepoint Unicode.
- Avant toute correction massive de texte:
  - lister les fichiers impactes;
  - appliquer des corrections ciblees fichier par fichier;
  - relire le `git diff` avant validation finale.

