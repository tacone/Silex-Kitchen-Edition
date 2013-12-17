Silex Propel - Kitchen Sink Edition
===================================

Yet another fork of the popular [Silex Kitchen Sink Edition](http://lyrixx.github.com/Silex-Kitchen-Edition)

Provides Propel and a bunch of other useful things.

## Features

- PropelORM 1.x, preconfigured on mysql's `test` database.
- Twitter Bootstrap + Assetic preconfigured to dump less files into CSS
- Web Debug Toolbar, with a own made add-on to display Propel Queries just as it normally does on Symphony

## Installation

### Permissions

Download it with composer and run `php composer.phar update`.

On Ubuntu and similar systems you can fix the permissions of folders by running:
```bash
sudo bin/misc/fix-ubuntu-permissions
```
If you use another OS please take a look at the ```fix-ubuntu-permissions``` source and make the folders writable yourself.

### Propel warm-up

**First**, take a look to the configuration files in `resources/config`

- `build.properties` contains the default propel config.
- `runtime-conf.xml` contains the infos to connect to the db
- `schema.xml` contains the database schema and the PHP namespace to associate to the PHP classes

**Second***, need to tell propel to generate the *models*. You can do it easily using the `bin/propel` bash script:

```bash
bin/propel main 
```

**Third**: create the database (the default database is `test`). **Warning**: this will **erase** parts of your configured database.

```bash
bin/propel insert-sql
```
