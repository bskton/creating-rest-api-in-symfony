FROM php:7.2.12-cli-alpine3.8

RUN mkdir -p /usr/local/bin \
  && curl -LsS https://symfony.com/installer -o /usr/local/bin/symfony \
  && chmod a+x /usr/local/bin/symfony

WORKDIR /app

CMD ["php", "bin/console", "server:run", "0.0.0.0:8000"]