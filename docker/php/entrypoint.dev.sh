#!/usr/bin/env bash
set -eux

supervisord -c /etc/supervisor/supervisord.conf

env >> /etc/environment
cron

php -d variables_order=EGPCS /var/www/html/artisan octane:start \
  --server=frankenphp \
  --host=localhost \
  --log-level \
  --watch \
  --port=8000 \
  --admin-port=2019
