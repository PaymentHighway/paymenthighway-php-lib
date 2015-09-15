#!/usr/bin/env bash
php -v

DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

${DIR}/../bin/phpunit ${DIR}/${1}