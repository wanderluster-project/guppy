FROM ubuntu:20.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get -y update && \
    apt-get -y upgrade && \
    apt-get --no-install-recommends -yq install \
        vim \
        git \
        ca-certificates \
        php \
        php-json \
        php-mbstring \
        php-redis \
        php-xml


CMD tail -f /dev/null