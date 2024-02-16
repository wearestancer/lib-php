# syntax=docker/dockerfile:1.3

ARG DOCKER_REGISTRY=docker.io/wearestancer
ARG PHP_VERSION=8.1

FROM ${DOCKER_REGISTRY}/php:${PHP_VERSION} AS main

CMD [ "sleep", "infinity" ]

RUN --mount=type=cache,target=/var/cache/apt,sharing=locked \
    --mount=type=cache,target=/var/log/apt,sharing=locked \
    --mount=type=cache,target=/var/lib/apt,sharing=locked apt update \
      && apt upgrade -y \
      && apt install -y --no-install-recommends gpg sudo vim \
      && apt autoremove -y \
      && sed -i 's/%sudo.*/%sudo   ALL=(ALL:ALL) NOPASSWD: ALL/' /etc/sudoers

# Create user
RUN useradd --create-home --shell /bin/bash --groups sudo vscode -u 1000
USER vscode
