php bin/console doctrine:migrations:migrate --no-interaction
php bin/console cache:clear
php bin/console cache:warmup