#!/bin/sh

# Install repositories
rpm -Uvh https://mirror.webtatic.com/yum/el6/latest.rpm

# Install packages
yum -y install nginx mysql55w mysql55w-server redis memcached
yum -y install php71w-common php71w-devel php71w-cli php71w-fpm php71w-mbstring php71w-mcrypt php71w-xml php71w-mysqlnd php71w-pecl-imagick php71w-pecl-memcached

# PHP configuration
sed -i 's/^;date.timezone.*=.*$/date.timezone = UTC/' /etc/php.ini
sed -i 's/^upload_max_filesize.*=.*$/upload_max_filesize = 20M/' /etc/php.ini
sed -i 's/^post_max_size.*=.*$/post_max_size = 20M/' /etc/php.ini

# Copy configs
rm -rf /etc/nginx/conf.d/*
cp -r /home/vagrant/htdocs/vagrant/conf/* /etc

# stop services
service php-fpm stop
service nginx stop
service mysqld stop
service memcached stop

# Start services
service php-fpm start
service nginx start
service mysqld start
service memcached start

# Autostart services
chkconfig php-fpm on
chkconfig nginx on
chkconfig mysqld on
chkconfig memcached on

# Install composer
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
chmod +x /usr/local/bin/composer
