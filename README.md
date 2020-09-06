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

Links
===================

- [Libreria Prooph](http://getprooph.org/)


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
$ docker-compose run --rm php-fpm php bin/console event-store:event-stream:create
```

### Load default items and coins

```sh
$ docker-compose run --rm php-fpm php bin/console vending-machine:load
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

API
===================

The endpoints allows responses as JSON and XML. You can choose the request and response format using the HTTP header negotiation.

That's mens the Content-Type header is used for define the request format and the Accept header for the response.

You can try the API directly from the swagger implemented in the localhost or using any REST application like Postman.


```sh
Content-Type application/json
Accept application/xml
```

The diferent endpoints implemented:

```console

CUSTOMERS:

POST /api/coin/insert/{coin}         Insert new coin inside the vending machine.
GET /api/coin/return                 Return all inserted coins.
GET /api/coin/status                 Check how much money I have inserted.
GET /api/item/buy/{name}             Buy an existing Item.
GET /api/item/status                 Check items info, price and amount of each one.

SERVICE:

PUT /api/service/item/{name}         Tool for set the price or amount of each Item.
PUT /api/service/coin/{coin}         Tool for set the amount of each Coin.
GET /api/service/status              Check status of everything
```

Considerations
===================
* The Item price must be a value between [0..9.99]
* The Item Amount or Coin Amout must be a value [0..99]
* The Items can be WATER, JUICE and SODA
* When you buy a Item, the vending machine never returns coins of 1. It's a requirement.


Test
===================

```sh
$ docker-compose run --rm php-fpm ./bin/phpunit
```
