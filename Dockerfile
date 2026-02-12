FROM php:8.3-cli


COPY . .

RUN apt-get update && apt-get install -y unzip
RUN curl -sS https://getcomposer.org/installer | php
RUN php composer.phar install

CMD php -S 0.0.0.0:$PORT -t api api/index.php

