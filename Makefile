start:
	symfony serve

load-fixtures:
	php bin/console doctrine:fixtures:load --quiet

doctrine-schema-update:
	php bin/console doctrine:schema:update --force

delete-cache:
	rm -rf var/cache

cache-clear: delete-cache
	php bin/console cache:clear

install: database

database:
	php bin/console doctrine:database:create

.PHONY: load-fixtures doctrine-schema-update database install