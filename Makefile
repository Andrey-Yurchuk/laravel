.PHONY: phpstan phpcs quality

phpstan:
	docker-compose exec app composer analyse

phpcs:
	docker-compose exec app composer check-style

quality:
	docker-compose exec app composer quality

