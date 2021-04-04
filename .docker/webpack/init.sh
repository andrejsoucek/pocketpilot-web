#!/bin/sh
if [ -z "$(ls /pocketpilot/node_modules)" ]; then # npm bug workaround; initialize node_modules
	npm i >/dev/null 2>&1
fi
npm i && npm run dev
