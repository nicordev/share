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

.PHONY: load-fixtures doctrine-schema-update