main:
	@echo 'Available commands: run-dev, run-global, stop, update-fosjs'

run-dev:
	php bin/console server:start
	docker-compose up -d

run-global:
	php bin/console server:start 0.0.0.0:8000
	docker-compose up -d

stop:
	php bin/console server:stop
	docker-compose stop

update-fosjs:
	php bin/console fos:js-routing:dump --format=json --target=public/js/fos_js_routes.json