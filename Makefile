.PHONY: help
help:
	@awk 'BEGIN {FS = ":.*##"; printf "Usage: make <target>\n"} /^[A-Za-z-]+:.*?##/ { printf "  %-15s %s\n", $$1, $$2 }' $(MAKEFILE_LIST)

.PHONY: prep up down

prep:
	@mkdir -p log temp node_modules
	@[ -f app/config/config.local.neon ] || cp app/config/config.local.neon-example app/config/config.local.neon

up: prep ## Start dockers
	@USER_ID=`id -u` GROUP_ID=`id -g` DOCKER_BUILDKIT=1 docker-compose up

down: ## Stop dockers
	docker-compose down

build: ## Make production build
	docker build -t pocketpilot-web --target production -f .docker/app/Dockerfile .

run-db: ## Start production database
	[ -n "$$(docker network ls --filter name=pocketpilot -q)" ] || docker network create pocketpilot
	[ -n "$$POCKETPILOT_CONFIG_DIR" ] || POCKETPILOT_CONFIG_DIR=/etc/pocketpilot && \
	[ -n "$$POCKETPILOT_DATA_DIR" ] || POCKETPILOT_DATA_DIR=/var/lib/pocketpilot/data && \
	password=`awk '/\s+password:/ {print $$2}' "$$POCKETPILOT_CONFIG_DIR/config.local.neon"` && \
	docker run --network="pocketpilot" --name postgis -d --restart unless-stopped \
		-e POSTGRES_USER=postgres \
		-e POSTGRES_PASSWORD="$$password" \
		-e POSTGRES_DB=pocketpilot \
		-v "$$POCKETPILOT_CONFIG_DIR/fixtures/init.sql:/docker-entrypoint-initdb.d/init.sql" \
		-v "$$POCKETPILOT_CONFIG_DIR/fixtures/airspace.sql:/docker-entrypoint-initdb.d/airspace.sql" \
		-v "$$POCKETPILOT_CONFIG_DIR/fixtures/elevation.sql:/docker-entrypoint-initdb.d/elevation.sql" \
		-v "$$POCKETPILOT_DATA_DIR:/var/lib/postgresql/data" \
		postgis/postgis:13-3.1

run-app: ## Start production app
	[ -n "$$(docker network ls --filter name=pocketpilot -q)" ] || docker network create pocketpilot
	[ -n "$$POCKETPILOT_CONFIG_DIR" ] || POCKETPILOT_CONFIG_DIR=/etc/pocketpilot && \
	docker run --network="pocketpilot" -p 80:80 --name pocketpilot-web -d --restart unless-stopped \
		-v "$$POCKETPILOT_CONFIG_DIR/config.local.neon:/pocketpilot/app/config/config.local.neon" \
		pocketpilot-web
