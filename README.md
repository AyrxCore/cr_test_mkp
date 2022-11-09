# MarketPlace

## Requirements

- PHP >= 8
- Composer - [Install](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)
- Yarn - [Install](https://classic.yarnpkg.com/en/docs/install#debian-stable)
- Node >= 14
- Linux

## Install

```sh
$ docker-compose build
```

## Run project

```sh
$ docker-compose up
```

Access to http://localhost:8087


## Add a new user

```sh
$ php bin/console user:add {email} {password}
```

## Promote user

```sh
$ php bin/console user:promote {email} {role}
```

## Demote user

```sh
$ php bin/console user:demote {email} {role}
```

## Test unitaires et fonctionnelles
Afin de lancer les tests
 `sh runtest.sh`
