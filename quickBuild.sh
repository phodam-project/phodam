#!/bin/sh
SCRIPT=$(readlink -f "$0")
ROOT=$(dirname "$SCRIPT")

CMD="cd /opt && echo test && composer install && composer run test"

echo "running lint/test"
docker build -t phodam-8.4-build:latest --build-arg "SOURCE_IMAGE=php:8.4-cli" -f build-container.Dockerfile .
docker run -it -v $ROOT:/opt phodam-8.4-build:latest /bin/bash -c "${CMD}"