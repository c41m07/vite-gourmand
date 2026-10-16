.DEFAULT_GOAL := help
.PHONY: help validate doctor start boot up build rebuild restart stop down status ps logs log logs-tail connect shell exec upgrade phpstan chmod fixer twigcs dev-dependencies watch gitignore controller entity form migrate migration fixtures-reset check-security test gource external-check lint-php lint-twig lint check cache-clear install assets assets-dev setup console composer about routes db-check db-status db-shell lint-config cache-warmup

# Commandes Docker communes ; les préparations de données restent explicites.
DC = $(DOCKER_COMPOSE)
SERVICE ?= app
TAIL ?= 100
CMD ?=

help: ## Afficher les commandes disponibles
	@awk 'BEGIN {FS = ":.*## "} /^[a-zA-Z0-9_-]+:.*## / {printf "  %-22s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

validate: ## Valider Compose sans afficher les variables sensibles
	$(DC) config --quiet
doctor: ## Vérifier Docker, Compose et les dépendances externes
	@command -v docker >/dev/null || { echo 'Docker est introuvable.' >&2; exit 1; }
	@docker info >/dev/null
	$(MAKE) validate
	$(MAKE) external-check
start: validate external-check ## Démarrer les services sans migration ni import
	$(DC) up -d --wait
boot: start ## Alias de start
up: start ## Alias de start
build: validate ## Construire les images locales, sans démarrer les services
	$(DC) build
rebuild: ## Reconstruire les images puis démarrer les services
	$(MAKE) build
	$(MAKE) start
restart: validate external-check ## Redémarrer les conteneurs, sans compilation ni watch
	$(DC) restart
stop: ## Arrêter uniquement ce projet, en conservant les conteneurs et données
	$(DC) stop
down: ## Retirer conteneurs et réseaux internes, conserver les volumes
	$(DC) down
status: ## Afficher les services, leur état et leurs ports
	$(DC) ps -a
ps: status ## Alias de status
logs: ## Suivre les logs du projet ; Ctrl+C pour quitter
	$(DC) logs -f --tail=$(TAIL)
log: logs ## Alias de logs
logs-tail: ## Afficher les derniers logs sans les suivre
	$(DC) logs --tail=$(TAIL)
connect: ## Ouvrir un shell dans SERVICE (service applicatif par défaut)
	$(DC) exec $(SERVICE) sh
shell: connect ## Alias de connect
exec: ## Exécuter CMD dans SERVICE, sans terminal interactif
	@$(if $(strip $(CMD)),:,echo 'Usage : make exec CMD="commande arguments" [SERVICE=nom]' >&2; exit 1)
	$(DC) exec -T $(SERVICE) $(CMD)
upgrade: rebuild ## Alias historique de rebuild ; ne met pas les dépendances à niveau

DOCKER_COMPOSE = docker compose
EXEC_APP = $(DOCKER_COMPOSE) exec -T app

###############
# Dev Command #
###############

phpstan: ## Analyser le PHP sans correction
	$(EXEC_APP) ./vendor/bin/phpstan analyse -c phpstan.dist.neon

chmod: ## DANGER : appliquer les permissions 777 aux fichiers
	$(EXEC_APP) chmod -R 777 ./*

fixer: ## Corriger les styles PHP et Twig (modifie les sources)
	$(EXEC_APP) ./vendor/bin/php-cs-fixer fix --show-progress="dots" -v
	$(EXEC_APP) ./vendor/bin/twig-cs-fixer lint --fix --no-cache --config=.twig-cs-fixer.dist.php

twigcs: ## Vérifier Twig avec twigcs
	$(EXEC_APP) ./vendor/bin/twigcs templates/

dev-dependencies: ## Préparer dépendances, base et assets (modifie la base)
	$(EXEC_APP) composer install
	$(EXEC_APP) npm install
	$(EXEC_APP) symfony console d:d:c --if-not-exists
	$(EXEC_APP) symfony console d:m:m --no-interaction
	$(EXEC_APP) npm run dev

watch: ## Recompiler les assets en continu ; Ctrl+C pour quitter
	$(EXEC_APP) npm run watch

gitignore: ## DANGER : réindexer le dépôt et créer un commit
	git rm -r --cached .
	git add .
	git commit -m ".gitignore est maintenant fonctionnel"

controller: ## Générer un contrôleur : name=NomController
	$(EXEC_APP) symfony console make:controller $(name) --no-interaction

entity: ## Générer une entité : name=Nom
	$(EXEC_APP) symfony console make:entity $(name) --no-interaction

form: ## Générer un formulaire : name=NomType entity=Nom
	$(EXEC_APP) symfony console make:form $(name) $(entity) --no-interaction

migrate: ## Appliquer les migrations (modifie la base)
	$(EXEC_APP) symfony console d:m:m --no-interaction

migration: ## Générer une migration (écrit un fichier)
	$(EXEC_APP) symfony console make:migration --no-interaction

fixtures-reset: ## DANGER : recréer la base et charger les fixtures
	$(EXEC_APP) symfony console d:d:d --force --if-exists
	$(EXEC_APP) symfony console d:d:c --if-not-exists
	$(EXEC_APP) symfony console d:m:m --no-interaction
	$(EXEC_APP) symfony console doctrine:fixtures:load --no-interaction

check-security: ## Contrôler les dépendances avec Symfony CLI
	$(EXEC_APP) symfony check:security

test: ## Exécuter PHPUnit sur la configuration de test existante
	$(EXEC_APP) ./vendor/bin/phpunit --testdox

##################
# UI Command #
##################

gource: ## Afficher l’historique Git avec Gource
	gource -1920x1080 -f --seconds-per-day 1 --camera-mode overview --disable-auto-rotate --dir-name-depth 3 --highlight-dirs --key --font-size 18 --padding 1.2 --hide filenames,progress,mouse

external-check: ## Vérifier les services et réseaux partagés
	@test -n "$$(docker ps -q --filter label=com.docker.compose.project=traefik --filter label=com.docker.compose.service=traefik)" || { echo 'Service partagé traefik absent : make -C /home/caim/traefik start' >&2; exit 1; }
	@test -n "$$(docker ps -q --filter label=com.docker.compose.project=mailhog --filter label=com.docker.compose.service=mailhog)" || { echo 'Service partagé mailhog absent : make -C /home/caim/mailhog start' >&2; exit 1; }
	@docker network inspect traefik_network >/dev/null 2>&1 || { echo 'Réseau externe traefik_network absent' >&2; exit 1; }
	@docker network inspect mail_network >/dev/null 2>&1 || { echo 'Réseau externe mail_network absent' >&2; exit 1; }

lint-php: ## Vérifier le style PHP sans correction
	$(EXEC_APP) ./vendor/bin/php-cs-fixer fix --dry-run --diff --using-cache=no
lint-twig: ## Vérifier le style Twig sans correction
	$(EXEC_APP) ./vendor/bin/twig-cs-fixer lint --no-cache --config=.twig-cs-fixer.dist.php
lint: ## Vérifier les configurations, templates et styles sans correction
	$(EXEC_APP) php bin/console lint:yaml config
	$(EXEC_APP) php bin/console lint:twig templates
	$(EXEC_APP) php bin/console lint:container
	$(MAKE) lint-php
	$(MAKE) lint-twig
check: ## Exécuter lint et PHPStan sans corriger les sources
	$(EXEC_APP) composer validate --no-check-publish
	$(MAKE) lint
	$(MAKE) phpstan
cache-clear: ## Vider le cache Symfony via le framework
	$(EXEC_APP) php bin/console cache:clear
install: ## Installer les dépendances PHP et Node, sans migration
	$(EXEC_APP) composer install --no-interaction
	$(EXEC_APP) npm ci
assets: ## Compiler les assets de production
	$(EXEC_APP) npm run build
assets-dev: ## Compiler les assets de développement
	$(EXEC_APP) npm run dev
setup: start ## Préparer dépendances, base et assets (modifie la base)
	$(MAKE) dev-dependencies

console: ## Exécuter Symfony : make console CMD="about"
	$(DC) exec -T app php bin/console $(if $(strip $(CMD)),$(CMD),about)

composer: ## Exécuter Composer : make composer CMD="validate"
	$(DC) exec -T app composer $(if $(strip $(CMD)),$(CMD),--version)

about: ## Afficher les informations Symfony
	$(DC) exec -T app php bin/console about

routes: ## Afficher les routes Symfony
	$(DC) exec -T app php bin/console debug:router

db-check: ## Vérifier la connexion SQL sans modifier les données
	$(DC) exec -T app php bin/console dbal:run-sql 'SELECT 1' --no-interaction

db-status: ## Afficher l’état des migrations sans les appliquer
	$(DC) exec -T app php bin/console doctrine:migrations:status --no-interaction

db-shell: ## Ouvrir une console SQL dans le service database
	$(DC) exec database sh -c 'export MYSQL_PWD="$$MARIADB_ROOT_PASSWORD"; exec mariadb -uroot'

lint-config: ## Valider les configurations Symfony et les templates
	$(DC) exec -T app php bin/console lint:yaml config
	$(DC) exec -T app php bin/console lint:twig templates
	$(DC) exec -T app php bin/console lint:container

cache-warmup: ## Préparer le cache Symfony
	$(DC) exec -T app php bin/console cache:warmup
