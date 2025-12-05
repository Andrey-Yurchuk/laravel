.PHONY: phpstan phpcs quality test test-coverage

phpstan:
	docker-compose exec app composer analyse

phpcs:
	docker-compose exec app composer check-style

quality:
	docker-compose exec app composer quality

test:
	docker-compose exec app composer test

test-coverage:
	docker-compose exec app composer test-coverage

