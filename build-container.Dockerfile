ARG SOURCE_IMAGE="php:7.4-cli"

FROM ${SOURCE_IMAGE}

run apt-get update && \
    apt-get install -y zip && \
    apt-get clean autoclean && \
    apt-get autoremove --yes && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
