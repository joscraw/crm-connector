 * * * * * /usr/bin/flock -n /tmp/my.lockfile php /var/www/wp-content/plugins/crm-connector/jobs/batch_contact_import_cron.php
 
* * * * * /usr/bin/flock -n /tmp/my.lockfile2 php /var/www/wp-content/plugins/crm-connector/jobs/batch_list_export_cron.php
 
 
 php -d xdebug.remote_autostart=On -d xdebug.remote_host=10.0.2.2 batch_contact_import_cron.php
 
 
 php -d xdebug.remote_autostart=On -d xdebug.remote_host=10.0.2.2 batch_list_export_cron.php




CREATE DATABASE honor_society DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;


GRANT ALL ON honor_society.* TO 'honor_society'@'localhost' IDENTIFIED BY 'xmZ$KeQ6L8$Q63@GTerzMt16';


ssh root@142.93.23.226

Mysql root n@cILEWDklJQoW&78GGqJcmT



sudo find . -type f -exec chmod 664 {} +
sudo find . -type d -exec chmod 775 {} +
sudo chmod 660 wp-config.php

