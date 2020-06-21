FROM wordpress:cli-php7.3
USER root

RUN apk add --no-cache bash subversion
RUN cd /opt/ && curl -sS https://getcomposer.org/installer | php
ADD composer.json /opt/
ADD composer.lock /opt/
RUN cd /opt && php /opt/composer.phar install --dev
ADD bin /var/www/html/bin
RUN bash /var/www/html/bin/install-wp-tests.sh wordpress wordpress wordpress db latest true

CMD "/opt/vendor/bin/phpunit"
