load-fixtures:
	php bin/console doctrine:fixtures:load --quiet

doctrine-schema-update:
	php bin/console doctrine:schema:update --force

.PHONY: load-fixtures doctrine-schema-update