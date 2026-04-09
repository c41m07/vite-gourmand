# Vite & Gourmand

Application Symfony 8 pour la gestion des menus, des commandes et des espaces utilisateur, employe et admin de Vite & Gourmand.

## Demarrage rapide

> Les commandes `make` se lancent depuis la racine du repo, cote hote.

### Prerequis

- Docker + Docker Compose
- Make
- WSL si vous travaillez depuis Windows sur `\\wsl.localhost\\...`
- Docker Desktop avec l'integration WSL active si vous lancez le projet depuis WSL

### Lancer le projet

```bash
make start
```

Cette commande demarre les containers, installe les dependances, prepare la base et compile les assets en mode dev.

Si `docker` n'est pas accessible depuis votre distribution WSL, activez l'integration WSL de Docker Desktop avant d'utiliser les commandes `make`.

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
- [`docs/technical/setup.md`](docs/technical/setup.md)
- [`docs/technical/architecture.md`](docs/technical/architecture.md)
- [`docs/database/data-model.md`](docs/database/data-model.md)
- [`docs/design/ui-guide.md`](docs/design/ui-guide.md)
- [`docs/project/roadmap.md`](docs/project/roadmap.md)
- [`docs/references/ecf-studi.md`](docs/references/ecf-studi.md)
- [`docs/references/ecf-studi.pdf`](docs/references/ecf-studi.pdf)

## Structure utile

- `app/`: application Symfony
- `docker/`: image PHP/Apache de dev
- `docs/`: documentation de dev + references ECF
- `sql/`: exports SQL attendus a terme
