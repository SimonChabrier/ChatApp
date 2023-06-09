# Real Time Multichannel Chat (MVP_version)

- Symfony 5.4
- Mercure Hub

## Avril 2023

## Try the app
https://chat.simschab.cloud/


User may be registered to the app. They can create channels and chat in them. 
Messages are sent in real time using Mercure.
Chat channels conversations are persisted in database using Doctrine an Messenger as transport async.

## Installation

- Clone the project

- Have a look on mecure hub documentation from : https://mercure.rocks/docs/getting-started
- Mercure Hub direct download link : https://github.com/dunglas/mercure/releases

- Download the MercureHub in the root of the project.

- Update the CaddyFile.dev with this to allow all orignins in dev environment for your tests.

```shell
http://localhost:3000 {
    route {
        mercure {
            transport_url {$MERCURE_TRANSPORT_URL:bolt://mercure.db}
            publisher_jwt !ChangeThisMercureHubJWTSecretKey!
            subscriber_jwt !ChangeThisMercureHubJWTSecretKey!
            cors_origins *
            publish_origins *
            demo
            anonymous
            subscriptions
            {$MERCURE_EXTRA_DIRECTIVES}
        }
        respond "Not Found" 404
    }
}
```

- Create your .env.local file and add your database credentials.

- Add the mercure hub jwt secret key in the .env.local file.

```
###> mercure ###
MERCURE_URL=http://localhost/.well-known/mercure
MERCURE_PUBLIC_URL=http://localhost/.well-known/mercure
#MERCURE_JWT_SECRET=!ChangeThisMercureHubJWTSecretKey!
MERCURE_TOKEN='GO_TO_JWT.IO_AND_GENERATE_A_TOKEN_USING_THIS_SECRET_KEY'
###< mercure ###
```

- Follow this [link to create your token](https://jwt.io/#debugger-io?token=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdfX0.iHLdpAEjX4BqCsHJEegxRmO-Y6sMxXwNATrQyRNt3GY) 


- paste this secret key : `!ChangeThisMercureHubJWTSecretKey!` in "VERIFY SIGNATURE" input.
and paste this payload : 

```shell
{
  "mercure": {
    "publish": [
      "*"
    ],
    "subscribe": [
      "*"
    ]
  }
}
```

- click to generate the token go back to your Symfony app and copy it in the .env.local file at the following line:

```
MERCURE_TOKEN='pasteyourtokenhere'
```

- update js script in home/index.html.twig and channel/show.html.twig with your mercure hub url : 
- `http://localhost/.well-known/mercure`


## Install the dependencies and create the database

```shell
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

You have dowloaded Mercure Hub in the root of the project. Go to Mercure directory and run it with the following command in terminal (osx - linux)):

```shell
MERCURE_PUBLISHER_JWT_KEY='!ChangeThisMercureHubJWTSecretKey!' \
MERCURE_SUBSCRIBER_JWT_KEY='!ChangeThisMercureHubJWTSecretKey!' \
./mercure run --config Caddyfile.dev
```

(windos)
    
```shell
$env:MERCURE_PUBLISHER_JWT_KEY='!ChangeThisMercureHubJWTSecretKey!'; $env:MERCURE_SUBSCRIBER_JWT_KEY='!ChangeThisMercureHubJWTSecretKey!'; .\mercure.exe run --config Caddyfile.dev
```

## Usage
start the symfony server with not -tls the following command in terminal:

```shell
symfony server:start --no-tls -d
```

start the messenger worker with the following command in terminal to persist the messages in database:

```shell
bin/console messenger:consume async
```

## Go on the app that's it

- create your user account
- create a channel
- publish a message in the channel...

## Documentation


- [Mercure Quick Start](https://mercure.rocks/docs/getting-started)
- [Mercure](https://mercure.rocks/docs/hub/install)
- [Caddy - The server used by the MercureHub](https://caddyserver.com/docs/)
- [Symfony](https://symfony.com/doc/current/mercure.html)

## Bonus create a systemd service to run the messenger worker (ubuntu)

- Crete the service file :

```shell
sudo nano /etc/systemd/system/my-messenger-worker.service
```

- Add the following content in the file :

```
[Unit]
Description=My Personnal Messenger worker

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=php /var/www/html/NAME_OF_YOUR_APP_DIRECTORY_HERE/bin/console messenger:consume async -vv --time-limit=3600
TimeoutStopSec=3600

[Install]
WantedBy=multi-user.target
```

- Restart systemd :

```
sudo systemctl daemon-reload
sudo systemctl restart my-messenger-worker.service
```

- Check the status of the service :

```
sudo systemctl status my-messenger-worker.service
```

- Enable the service to start at boot :

```
sudo systemctl enable my-messenger-worker.service
```

- finsih the worker will start at boot and will restart if it crash and always run in background to persist the messages in database asynchroneously.