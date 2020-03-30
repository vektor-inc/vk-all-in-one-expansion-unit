FROM wordpress:cli-php7.3
#WORKDIR /opt
USER root

RUN apk add --no-cache mysql-client bash subversion less vim
RUN chown www-data /opt
USER www-data
RUN cd /opt/ && curl -sS https://getcomposer.org/installer | php
RUN cd /var/www/html && php /opt/composer.phar require "phpunit/phpunit"
ADD bin /var/www/html/bin
RUN bash /var/www/html/bin/install-wp-tests.sh wordpress wordpress wordpress db latest true

CMD "vendor/bin/phpunit"
