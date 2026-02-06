DOCKER_COMPOSE = docker compose
EXEC_APP = $(DOCKER_COMPOSE) exec -it app
CONNECT_APP = $(EXEC_APP) /bin/bash

###############
# Dev Command #
###############

check: fixer phpstan # twigcs

phpstan:
	$(EXEC_APP) ./vendor/bin/phpstan analyse -c phpstan.dist.neon

chmod:
	$(EXEC_APP) chmod -R 777 ./*

fixer:
	$(EXEC_APP) ./vendor/bin/php-cs-fixer fix --show-progress="dots" -v
	$(EXEC_APP) ./vendor/bin/twig-cs-fixer lint --fix --no-cache --config=.twig-cs-fixer.dist.php

twigcs:
	$(EXEC_APP) ./vendor/bin/twigcs templates/

dev-dependencies:
	$(EXEC_APP) composer install
	$(EXEC_APP) npm install
	$(EXEC_APP) symfony console d:d:c --if-not-exists
	$(EXEC_APP) symfony console d:m:m --no-interaction
	$(EXEC_APP) npm run dev

watch: ## Watch assets
	$(EXEC_APP) npm run watch

gitignore:
	git rm -r --cached .
	git add .
	git commit -m ".gitignore est maintenant fonctionnel"

controller:
	$(EXEC_APP) symfony console make:controller $(name)

entity:
	$(EXEC_APP) symfony console make:entity $(name)

form:
	$(EXEC_APP) symfony console make:form $(name) $(entity)

migrate:
	$(EXEC_APP) symfony console d:m:m --no-interaction

migration:
	$(EXEC_APP) symfony console make:migration

check-security:
	$(EXEC_APP) symfony check:security

cache-clear:
	rm -rf app/var/cache/

##################
# Docker Command #
##################

upgrade:
	$(DOCKER_COMPOSE) stop
	$(DOCKER_COMPOSE) build
	$(DOCKER_COMPOSE) up -d

boot:
	$(DOCKER_COMPOSE) up -d

connect:
	$(CONNECT_APP)

start: boot dev-dependencies

stop: ## Stop containers
	$(DOCKER_COMPOSE) stop

restart: stop start cache-clear watch

log:
	$(DOCKER_COMPOSE) logs -f

test:
	$(EXEC_APP) ./vendor/bin/phpunit --testdox