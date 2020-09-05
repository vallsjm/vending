VENDING MACHINE
===================

Instalación del Proyecto
===================

### Clonar Proyecto

```sh
$ git clone https://github.com/vallsjm/vending.git
```

### Instalar las dependencias de PHP via Composer

```sh
$ docker-compose up -d php-fpm
$ docker-compose run --rm php-fpm php composer.phar install -o --prefer-dist --no-interaction
```

### Levantar todos los contenedores Docker

```sh
$ docker-compose build
$ docker-compose up -d --force-recreate
```

### Crear el event strem inicial

```sh
docker-compose run --rm php-fpm php bin/console event-store:event-stream:create
```
### Ya disponible

```sh
http://localhost
```

Pruebas
===================

Es cierto que no están muy trabajadas, no tanto como me abría gustado.


```sh
docker-compose run --rm php-fpm ./bin/phpunit
```
