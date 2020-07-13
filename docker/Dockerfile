FROM php:7-fpm



RUN apt-get update && \

    apt-get install -y \

        zlib1g-dev libzip-dev sendmail





RUN echo "sendmail_path=/usr/sbin/sendmail -t -i" >> /usr/local/etc/php/conf.d/sendmail.ini



RUN docker-php-ext-install pdo_mysql

RUN docker-php-ext-install zip



RUN sed -i '/#!\/bin\/sh/aservice sendmail restart' /usr/local/bin/docker-php-entrypoint

RUN sed -i '/#!\/bin\/sh/aecho "$(hostname -i)\t$(hostname) $(hostname).localhost" >> /etc/hosts' /usr/local/bin/docker-php-entrypoint



# And clean up the image

RUN rm -rf /var/lib/apt/lists/*

# Run the command on container startup
CMD php checker.php
