FROM debian

RUN apt-get update && apt-get install -y apache2

ENV APACHE_LOCK_DIR="/var/lock"
ENV APACHE_PID_FILE="/var/run"
ENV APACHE_RUN_USER="www-data"
ENV APACHE_RUN_GROUP="www-data"
ENV ENV APACHE_LOG_DIR="/var/log"

RUN apt install -y php libapache2-mod-php php-cli php-cgi php-mysql curl zip unzip

RUN curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
RUN apt-get install -y php-dom php-curl php-xml php-zip
RUN php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer

COPY ./laravel.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

RUN apt-get install -y php-dom php-json php-mbstring php-tokenizer php-xml php-xmlwriter

EXPOSE 80
RUN service apache2 restart
CMD /usr/sbin/apachectl -D FOREGROUND