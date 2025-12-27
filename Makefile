.PHONY: phpstan phpcs quality test test-coverage clear-cache warm-cache restart

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

clear-cache:
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan route:clear
	docker-compose exec app php artisan view:clear
	docker-compose exec app php artisan optimize:clear

warm-cache:
	docker-compose exec app php artisan cache:warm

restart:
	docker-compose exec app php artisan octane:reload

