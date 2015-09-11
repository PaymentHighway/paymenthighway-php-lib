#!/usr/bin/env bash
php -v

DIR=$(dirname $(readlink -f $0))

${DIR}/../bin/phpunit ${DIR}/${1}