# En cours de rédaction 25 Avril 2023

## Multichannel Chat with Mercure

User may be registered to the app. They can create channels and chat in them. 
Messages are sent in real time using Mercure.
Chat channels conversations are persisted in database using Doctrine an Messenger as transport async.

## Installation

- Clone the project

- Download the mecure hub from : https://mercure.rocks/docs/getting-started

Create your .env.local file and add your database credentials.

```shell
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

Save in in the root of the project. and run it with the following command in terminal (osx - linux)):

```shell
MERCURE_PUBLISHER_JWT_KEY='!ChangeThisMercureHubJWTSecretKey!' \
MERCURE_SUBSCRIBER_JWT_KEY='!ChangeThisMercureHubJWTSecretKey!' \
./mercure run --config Caddyfile.dev
```

## Usage
start the symfony server with not -tls the following command in terminal:

```shell
symfony server:start --no-tls
```

start the messnger worker with the following command in terminal:

```shell
php bin/console messenger:consume async
```