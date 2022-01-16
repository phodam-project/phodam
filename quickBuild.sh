#!/bin/sh

docker build -t phodam-7.4-build:latest --build-arg "SOURCE_IMAGE=php:7.4-cli" -f build-container.Dockerfile .
docker build -t phodam-8.0-build:latest --build-arg "SOURCE_IMAGE=php:8.0-cli" -f build-container.Dockerfile .
docker build -t phodam-8.1-build:latest --build-arg "SOURCE_IMAGE=php:8.1-cli" -f build-container.Dockerfile .


SCRIPT=$(readlink -f "$0")
ROOT=$(dirname "$SCRIPT")

CMD="cd /opt && echo test && composer install && composer run lint && composer run test"

echo "running lint/test"
docker run -it -v $ROOT:/opt phodam-7.4-build:latest /bin/bash -c "${CMD}"
docker run -it -v $ROOT:/opt phodam-8.0-build:latest /bin/bash -c "${CMD}"
docker run -it -v $ROOT:/opt phodam-8.1-build:latest /bin/bash -c "${CMD}"
