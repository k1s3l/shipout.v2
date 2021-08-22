CURRENT_UID := $(shell id -u)
update = false

.PHONY: build up down restart test php node postgres composerUpdate meigrate

build:
	docker-compose build --build-arg UID=$(CURRENT_UID)

up:
	docker-compose up -d

down:
	docker-compose down

restart:
	docker-compose down && docker-compose up -d

test:
	docker-compose exec php vendor/bin/phpunit

php:
	docker-compose exec php /bin/bash

postgres:
	docker-compose exec postgres /bin/bash

node:
	docker-compose exec node /bin/bash

composerUpdate:
	docker-compose exec php composer update

migrate:
    ifeq ($(update), true)
		docker-compose exec php bin/console doctrine:schema:update --force
    else
		docker-compose exec php bin/console doctrine:migrations:migrate
    endif