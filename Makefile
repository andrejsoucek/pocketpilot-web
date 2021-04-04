.PHONY: help
help:
	@awk 'BEGIN {FS = ":.*##"; printf "Usage: make <target>\n"} /^[A-Za-z-]+:.*?##/ { printf "  %-15s %s\n", $$1, $$2 }' $(MAKEFILE_LIST)

.PHONY: prep up down

prep:
	@mkdir -p log temp node_modules vendor
	@[ -f app/config/config.local.neon ] || cp app/config/config.local.example.neon app/config/config.local.neon

up: prep ## Start dockers
	@USER_ID=`id -u` GROUP_ID=`id -g` DOCKER_BUILDKIT=1 docker-compose up

up-build: prep ## Rebuild and start dockers
	@USER_ID=`id -u` GROUP_ID=`id -g` DOCKER_BUILDKIT=1 docker-compose up --build

into-app: ## Go into the app container
	docker exec -ti pocketpilot_web_dev bash

into-db: ## Go into the db
	docker exec -ti pocketpilot_postgis_dev psql -U postgres -d pocketpilot

phpcs: ## Execute code sniffer check in the dev container
	docker exec -ti pocketpilot_web_dev composer cs

phpstan: ## Execute phpstan check in the dev container
	docker exec -ti pocketpilot_web_dev composer phpstan

down: ## Stop dockers
	docker-compose down

build: ## Make production build
	docker build -t pocketpilot-web --target production -f .docker/app/Dockerfile .

run-db: ## Start production database
	[ -n "$$(docker network ls --filter name=pocketpilot -q)" ] || docker network create pocketpilot
	[ -n "$$POCKETPILOT_CONFIG_DIR" ] || POCKETPILOT_CONFIG_DIR=/var/lib/pocketpilot/config && \
	[ -n "$$POCKETPILOT_DATA_DIR" ] || POCKETPILOT_DATA_DIR=/var/lib/pocketpilot/dbdata && \
	password=`awk '/\s+password:/ {print $$2}' "$$POCKETPILOT_CONFIG_DIR/config.local.neon"` && \
	docker run --network="pocketpilot" --name postgis -d --restart unless-stopped \
		-e POSTGRES_USER=postgres \
		-e POSTGRES_PASSWORD="$$password" \
		-e POSTGRES_DB=pocketpilot \
		-v "$$POCKETPILOT_CONFIG_DIR/fixtures/init.sql:/docker-entrypoint-initdb.d/init.sql" \
		-v "$$POCKETPILOT_DATA_DIR:/var/lib/postgresql/data" \
		postgis/postgis:13-3.1

run-app: ## Start production app
	[ -n "$$(docker network ls --filter name=pocketpilot -q)" ] || docker network create pocketpilot
	[ -n "$$POCKETPILOT_CONFIG_DIR" ] || POCKETPILOT_CONFIG_DIR=/var/lib/pocketpilot/config && \
	[ -n "$$POCKETPILOT_SSL_DIR" ] || POCKETPILOT_SSL_DIR=/var/lib/pocketpilot/letsencrypt && \
	[ -n "$$POCKETPILOT_MAPS_DIR" ] || POCKETPILOT_MAPS_DIR=/var/lib/pocketpilot/maps && \
	docker run --network="pocketpilot" -p 80:80 -p 443:443 --name pocketpilot-web -d --restart unless-stopped \
		-v "$$POCKETPILOT_CONFIG_DIR/config.local.neon:/pocketpilot/app/config/config.local.neon" \
		-v "$$POCKETPILOT_SSL_DIR:/home/letsencrypt" \
		-v "$$POCKETPILOT_MAPS_DIR:/pocketpilot/www/maps" \
		pocketpilot-web
