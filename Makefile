CURRENT_UID := $(shell id -u)

.PHONY: build up down restart test php node postgres composerUpdate

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
