FROM wordpress:cli-php7.3
USER root

RUN apk add --no-cache bash subversion
RUN cd /opt/ && curl -sS https://getcomposer.org/installer | php
RUN cd /opt && php /opt/composer.phar require --dev "phpunit/phpunit=7.5.9"
ADD bin /var/www/html/bin
RUN bash /var/www/html/bin/install-wp-tests.sh wordpress wordpress wordpress db latest true

CMD "/opt/vendor/bin/phpunit"
