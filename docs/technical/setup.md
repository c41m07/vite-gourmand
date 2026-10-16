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

Cette commande démarre les conteneurs sans migration, installation ni création de base. Pour la préparation explicite (modificatrice), utiliser `make setup`.

Démarrer d’abord `make -C /home/caim/traefik start` et `make -C /home/caim/mailhog start`. `make external-check` vérifie les services et réseaux partagés ; `make stop` ne les arrête pas.

## Commandes utiles

| Commande | Même usage dans les projets |
|---|---|
| `make` / `make help` | Afficher les commandes réellement disponibles |
| `make doctor` | Vérifier Docker, Compose et les dépendances partagées |
| `make validate` | Valider Compose sans afficher les secrets |
| `make start` / `make boot` / `make up` | Démarrer et attendre les services, sans migration ni import |
| `make build` | Construire les images sans démarrer les applications |
| `make rebuild` | Reconstruire puis démarrer les applications |
| `make restart` | Redémarrer les conteneurs, sans watch ni compilation |
| `make stop` | Arrêter seulement ce projet, conserver conteneurs et données |
| `make down` | Retirer conteneurs et réseaux internes, conserver les volumes |
| `make status` / `make ps` | Afficher état, services et ports |
| `make logs` / `make log` | Suivre les logs ; `TAIL=50` limite l’historique |
| `make logs-tail` | Afficher les derniers logs sans les suivre |
| `make connect` / `make shell` | Ouvrir un shell ; `SERVICE=database` change la cible |
| `make exec CMD="id"` | Exécuter une commande sans terminal interactif |

Les commandes `install`, `assets`, `assets-dev`, `watch`, `test`, `lint`, `fixer` et `check` sont exposées selon les outils existants. `lint` et `check` ne corrigent pas les sources ; `fixer` le fait explicitement. `setup`, `bootstrap`, `migrate`, les imports, fixtures et comptes restent des préparations séparées qui peuvent modifier les données. `upgrade` est un alias de `rebuild`, sans mise à niveau des dépendances. Pour les sources copiées dans une image (CV et Mémo Game), utiliser `make rebuild` après modification.

Sur les projets Symfony : `console CMD="about"`, `about`, `routes`, `lint-config`, `lint-twig`, `cache-clear`, `cache-warmup`, `db-check`, `db-status` et `db-shell`. `composer CMD="validate"` suit l’environnement du projet (hôte pour le portfolio, conteneur pour Diablo/Genesis/Vite & Gourmand). `migration` n’est proposé que si Maker est disponible. Le CV conserve Composer sur l’hôte et fournit `db-check`/`db-shell`, sans commandes Symfony.

Pour Vite & Gourmand : `make check` valide Composer puis lance les contrôles Symfony/style et PHPStan, sans correction. `make fixer` corrige PHP/Twig. `make install` installe Composer/npm sans migration ; `make assets` compile la production et `make assets-dev` le développement. `make restart` redémarre seulement les conteneurs : lancer `make watch` séparément si nécessaire. Les anciens raccourcis restent disponibles dans `make help`, avec leurs effets décrits.

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

### Ressources Docker conservées

Le projet Compose reste `vite-gourmand`. Les services sont `app` (Apache/PHP) et `database`, sans `container_name`. La base utilise les données applicatives existantes dans `/home/caim/Projets/studi/ECF/vite-gourmand/mariadb`, après bascule validée le 16 octobre 2026. Ne pas remplacer ce dossier par un dossier vide. L’ancien dossier `/home/caim/studi/ECF/vite-gourmand/mariadb`, sans base applicative, est conservé pour retour arrière. Les anciens noms DNS `ecf_vite_gourmand_app` et `ecf_vite_gourmand_database` restent des alias. MailHog est indépendant dans `/home/caim/mailhog` (projet `mailhog`), partagé avec le portfolio sur `mail_network`. Ports 1025/8025 et `http://mailhog.localhost` sont conservés. Docker : `smtp://mailhog:1025` ; exécution sur l’hôte : `smtp://127.0.0.1:1025`. Tous les e-mails arrivent dans la même boîte de développement, conservée en mémoire seulement. Aucun relais portfolio n’est nécessaire. Traefik reste dans `/home/caim/traefik`.
