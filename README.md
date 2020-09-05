VENDING MACHINE
===================

The goal of this API is to model a vending machine solution and the state it must maintain during its operation.

The machine works like all vending machines: it takes money then gives you items. The vending machine accepts money in the form of 0.05, 0.10, 0.25 and 1

The machine has three default items, WATER, JUICE and SODA that cost 0.65, 1.00, and 1.50. Also user may hit the button “return coin” to get back the money they’ve entered so far, If you put more money in than the item price, you get the item and change back.

The API is implementing DDD + CQRS + EventSourcing. It uses the framework Symfony and PROOPH components. In order to improve the performance this example doesn't use ODM or ORM mapping system.


Prerequisites
===================

- PHP 7.4
- Symfony 4.4.11
- Docker + Docker Compose

Install
===================

### Clone

```sh
$ git clone https://github.com/vallsjm/vending.git
$ cd vending
```

### Install composer dependencies

```sh
$ docker-compose up -d php-fpm
$ docker-compose run --rm php-fpm php composer.phar install -o --prefer-dist --no-interaction
```

### Wake up all containers

```sh
$ docker-compose build
$ docker-compose up -d --force-recreate
```

### Create the event strem

```sh
docker-compose run --rm php-fpm php bin/console event-store:event-stream:create
```

### Load default items and coins

```sh
docker-compose run --rm php-fpm php bin/console vending-machine:load
```


### Its Done!!

The API have a swagger with further information about each endpoint.

```sh
http://localhost
```

You can see the database using PhpMyAdmin.

```sh
http://localhost:8181
user: dev
password: 1234
```

Test
===================

```sh
docker-compose run --rm php-fpm ./bin/phpunit
```
