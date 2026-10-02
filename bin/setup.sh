set -euo pipefail
cd /var/www/html

bin/console plugin:refresh
bin/console plugin:install --activate DkProductLabel   # skips if already installed
bin/console plugin:update DkProductLabel                # runs new migrations on re-runs
bin/console cache:clear

bin/build-administration.sh
bin/build-storefront.sh

composer --working-dir=custom/plugins/DkProductLabel/tools install --no-interaction
echo "DkProductLabel installed. Storefront: http://localhost  Admin: http://localhost/admin"