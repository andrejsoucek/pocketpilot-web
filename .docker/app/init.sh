#!/bin/sh
su www-data -c "composer install" -s /bin/bash
supervisord --nodaemon --configuration /etc/supervisor/supervisord.conf
