# Ioiopay PHP SDK

## Install

```
$ composer require antonyflame/ioiopay
$ php artisan vendor:publish --provider='Antonyflame\Ioiopay\Laravel\IoiopayServiceProvider'
```

## Config

```
$ vim config/ioiopay.php
```

## test
```
$ cp -n tests/config.php-example tests/config.php
$ vim tests/config.php
$ php -S 0.0.0.0:8080 -t tests

Open a new terminal to expose to the internet
$ ngrok2 http 8080
```

