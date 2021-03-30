#!/bin/sh
if [ -z "$(ls /home/letsencrypt/.acme.sh)" ]; then
  su letsencrypt -c "mkdir /home/letsencrypt/cert"
  su letsencrypt -c "curl https://get.acme.sh -o /home/letsencrypt/acme.sh && chmod +x acme.sh" -s /bin/bash
  su letsencrypt -c "/home/letsencrypt/acme.sh email=andrejsoucek@seznam.cz --force && rm acme.sh" -s /bin/bash
  su letsencrypt -c "/home/letsencrypt/.acme.sh/acme.sh --issue -d pocketpilot.cz --nginx" -s /bin/bash
  su letsencrypt -c "/home/letsencrypt/.acme.sh/acme.sh --install-cert -d pocketpilot.cz --key-file /home/letsencrypt/cert/key.pem --fullchain-file /home/letsencrypt/cert.pem --reloadcmd \"service nginx force-reload\""
fi

while true; do
  sleep 86400
  /home/letsencrypt/.acme.sh/acme.sh --cron --home /home/letsencrypt/.acme.sh
done
