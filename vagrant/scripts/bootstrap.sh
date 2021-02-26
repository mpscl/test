#!/usr/bin/env bash

osUpdates()
{
  echo -e "\n-- Updating Linux Packages \n"
	dnf check-update
  dnf update -y
  dnf clean all
  dnf install -y nano vim wget curl net-tools lsof bash-completion
}
phpSetup()
{

  echo -e "\n-- Configuring PHP Modules \n"
  sudo dnf install php-xdebug php-devel php-pear php-mysqlnd php-cli php-common php-spl php-hash php-ctype php-json php-mbstring php-zip php-gd php-curl php-xml php-gmp php-intl php-xmlrpc php-bcmath php-soap php-ldap unzip -y

}
apacheSetup()
{
	echo -e "\n-- Installing Apache \n"
	sudo sudo mkdir /var/log/apache2/
	sudo cp /var/www/html/vagrant/config/etc/httpd/conf.d/prueba.conf /etc/httpd/conf.d/

}
databaseSetup()
{

	echo -e "\n-- Installing MariaDB Database Server \n"

  # Install MariaDB Server
  # install mariadb
  sudo dnf -y install mariadb-server

  sudo systemctl enable mariadb

  # ensure it is running
  sudo systemctl restart mariadb.service

  ### post-install setup

  # set root password
  sudo /usr/bin/mysqladmin -u root password 'password'

  # allow remote access (required to access from our private network host. Note that this is completely insecure if used in any other way)
  mysql -u root -ppassword -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY 'password' WITH GRANT OPTION; FLUSH PRIVILEGES;"

   # restart
  sudo systemctl restart mariadb.service

    # Start mariadb service automatically on boot
	systemctl enable mariadb &> /dev/null

}
databaseRestore()
{

	echo -e "\n-- Restoring Database Backups \n"

	# Restore latest database.gz
  mysql --user root -ppassword < /var/www/html/update_pro.sql

}
phpComposerSetup()
{

	echo -e "\n-- Installing PHP Composer \n"

	sudo php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
	sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
}
loadVendorCode()
{

	echo -e "\n-- Loading Vendor Code \n"

	export COMPOSER_ALLOW_SUPERUSER=1
	cd /var/www/html/core/
	/usr/local/bin/composer install --no-plugins
	/usr/local/bin/composer install

}
serviceStart()
{

	echo -e "\n-- Starting Services \n"
  sudo systemctl restart php-fpm
	sudo systemctl restart httpd

}
xdebugSetup()
{
# Creating the configuration for Xdebug
cat > /etc/php.d/30-xdebug.ini << EOF
zend_extension=xdebug.so
xdebug.default_enable=1
xdebug.remote_enable=1
xdebug.remote_handler=dbgp
xdebug.remote_connect_back=1
xdebug.remote_port=9000
xdebug.remote_autostart=1
EOF
}

osUpdates
phpSetup
apacheSetup
databaseSetup
phpComposerSetup
loadVendorCode
xdebugSetup
#databaseRestore
serviceStart
