start:
	symfony serve

composer-install:
	composer install

load-fixtures:
	php bin/console doctrine:fixtures:load --quiet

doctrine-schema-update:
	php bin/console doctrine:schema:update --force

delete-cache:
	rm -rf var/cache

cache-clear: delete-cache
	php bin/console cache:clear

install: composer-install database

database:
	php bin/console doctrine:database:create

.PHONY: composer-install load-fixtures doctrine-schema-update database install