
ARG VERSION

FROM git.priv.iliad78.net:5050/stancer/ci-template/php:${VERSION}

ARG USER="vscode"

RUN adduser --disabled-password --gecos "" $USER

RUN mkdir -p /usr/local/composer/cache/ && chown -R $USER:$USER /usr/local/composer/cache/

USER $USER
WORKDIR /home/$USER
