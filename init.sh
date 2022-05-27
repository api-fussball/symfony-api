#!/usr/bin/env bash

if [[ "$1" == "reinstall" ]]
then
    echo "Dropping Database Tables"
    php bin/console doctrine:database:drop --force --env=test
    php bin/console doctrine:database:drop --force
fi

php bin/console doctrine:database:create --no-interaction
php bin/console doctrine:migrations:migrate --no-interaction

php bin/console doctrine:database:create --no-interaction --env=test
php bin/console doctrine:migrations:migrate --no-interaction --env=test
