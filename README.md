# larama (Laravel Manager)

larama is a standalone Symfony console application for Laravel.

It borrows the "alias" concept from [drush](https://github.com/drush-ops/drush) so that you can run artisan commands from any directory for any site on a web server given a site alias. Additionally it provides convenience commands for artisan such as "db:cli", "db:drop", "db:dump" and "app:status".

## Commands

### db:cli

Runs `mysql`. Can be used to import a database dump.

```bash
zcat example_com.sql.gz | larama --site-alias=example db:cli
```

### db:dump

Provides a full database dump using `mysqldump` or `pg_dump`.

```bash
larama --site-alias=example db:dump --gzip --result-file=/path/to/example_com.sql
```

### db:drop

Drops all tables, views and indexes in a database without requiring database ownership permissions. Useful for lazy programmers who don't provide `::down()` in migrations. This will prompt for confirmation unless the `-y` option is provided.

```bash
larama --site-alias=example db:drop
```

### app:status

Prints out information about PHP and the current Laravel application. If no laravel application detected, then it will only print out PHP information.

```bash
larama --site-alias=example app:status
Laravel version     : 5.3.23
Site name           : Example
Site environment    : production
Site URI            : http://www.example.com
Database driver     : mysql
Database hostname   : localhost
Database port       : 3306
Database username   : forge
Database name       : forge
Laravel bootstrap   : Successful
Laravel root        : /var/www/www.example.com
Laravel app root    : /var/www/www.example.com/app
Filesystem driver   : local
Filesystem path     : /var/www/www.example.com/storage/app
Console application : artisan
Console version     : 5.3.23
PHP executable      : /usr/bin/php
PHP configuration   : /etc/php-cli.ini
PHP OS              : Linux
```

```bash
larama  app:status
Console application : larama (Laravel Manager)
Console version     : 0.1
PHP executable      : /usr/bin/php
PHP configuration   : /etc/php-cli.ini
PHP OS              : Linux
```
