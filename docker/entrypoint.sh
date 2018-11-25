#!/bin/sh
set -e

if [ ! -f "composer.json" ]; then
  symfony new app 3.4
fi

exec "$@"