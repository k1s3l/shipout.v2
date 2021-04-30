.PHONY: build up down restart test php composerInitProject composerUpdate

build:
	docker-compose build

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

composerInitProject:
	docker-compose exec php composer create-project symfony/skeleton .

composerUpdate:
	docker-compose exec php composer update
