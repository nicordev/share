load-fixtures:
	php bin/console doctrine:fixtures:load --quiet

doctrine-schema-update:
	php bin/console doctrine:schema:update --force

cache-clear:
	rm -rf var/cache
	php bin/console cache:clear

.PHONY: load-fixtures doctrine-schema-update