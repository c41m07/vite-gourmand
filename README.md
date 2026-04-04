# Vite & Gourmand

Application Symfony 8 pour la gestion des menus, des commandes et des espaces utilisateur, employe et admin de Vite & Gourmand.

## Demarrage rapide

> Les commandes `make` se lancent depuis la racine du repo, cote hote.

### Prerequis

- Docker + Docker Compose
- Make
- WSL si vous travaillez depuis Windows sur `\\wsl.localhost\\...`

### Lancer le projet

```bash
make start
```

Cette commande demarre les containers, installe les dependances, prepare la base et compile les assets en mode dev.

### Commandes utiles

```bash
make connect
make log
make test
make check
make watch
make migration
make migrate
make fixtures-reset
```

### Acces local

- Application: host defini par `TRAEFIK_URL_APP` dans `.env`
- MailHog: `http://localhost:8025`

## Documentation

- [`docs/README.md`](docs/README.md)
- [`docs/technical/_DEV_SETUP.md`](docs/technical/_DEV_SETUP.md)
- [`docs/technical/_DEV_ARCHITECTURE.md`](docs/technical/_DEV_ARCHITECTURE.md)
- [`docs/database/_DEV_DATA_MODEL.md`](docs/database/_DEV_DATA_MODEL.md)
- [`docs/design/_DEV_UI_GUIDE.md`](docs/design/_DEV_UI_GUIDE.md)
- [`docs/project/_DEV_ROADMAP.md`](docs/project/_DEV_ROADMAP.md)

## Structure utile

- `app/`: application Symfony
- `docker/`: image PHP/Apache de dev
- `docs/`: documentation nettoyee et orientee dev
- `sql/`: exports SQL attendus a terme
