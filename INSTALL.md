# Pocket Pilot Installation

## Development environment

Prerequisites:

- Linux compatible environment
- git to clone the repo
- docker up & running, permissions to build/start/stop containers, docker-compose

### Quick start
- clone the Pocket Pilot repository, cd to it
- run `make up`, it builds and starts 3 containers:
  - `pocketpilot-web_web_1` - the application itself, PHP source files are mounted from the repo and can be edited
  - `pocketpilot-web_webpack_1` - webpack JS bundle hot-reloading (./assets compiled to ./www/dist/ on the host)
  - `pocketpilot-web_postgis_1` - database
- connect with a web browser to http://localhost:8888/
  -  dev@pocketpilot.cz  dev (see .docker/fixtures/)

## Production environment
- build production image
```
make build
```
- prepare configuration
```
/etc/pocketpilot/config.local.neon
                /fixtures/airspace.sql
                /fixtures/elevation.sql
                /fixtures/init.sql
```
- prepare data directory
```
mkdir -p /var/lib/pocketpilot/data
chown 999:999 /var/lib/postgresql/data
```
- start dockers
```
make run-db
make run-app
```
(the default config/data dir can be redefined by 'POCKETPILOT_CONFIG_DIR' and POCKETPILOT_DATA_DIR)
