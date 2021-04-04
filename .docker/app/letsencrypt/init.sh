#!/bin/sh
set -e

export HOME=/home/letsencrypt

DOMAIN='pocketpilot.cz'

cd /home/letsencrypt

if [ ! -d /home/letsencrypt/.acme.sh ]; then
	mkdir -p /home/letsencrypt/cert /home/letsencrypt/wwwroot
	curl https://get.acme.sh -o /home/letsencrypt/acme.sh && chmod +x /home/letsencrypt/acme.sh
	/home/letsencrypt/acme.sh email=andrejsoucek@seznam.cz --force
	rm /home/letsencrypt/acme.sh
	/home/letsencrypt/.acme.sh/acme.sh --issue -d "$DOMAIN" -w /home/letsencrypt/wwwroot
	/home/letsencrypt/.acme.sh/acme.sh --install-cert -d "$DOMAIN" --key-file /home/letsencrypt/cert/key.pem --fullchain-file /home/letsencrypt/cert/cert.pem --reloadcmd "sudo service nginx force-reload"
fi

while true; do
  sleep 86400
  /home/letsencrypt/.acme.sh/acme.sh --cron --home /home/letsencrypt/.acme.sh || true
done
