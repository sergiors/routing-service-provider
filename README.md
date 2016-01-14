Routing Service Provider
------------------------
[![Build Status](https://travis-ci.org/sergiors/routing-service-provider.svg?branch=master)](https://travis-ci.org/inbep/routing-service-provider)

Import your routes from yaml, php files or directory.

Install
-------
```
composer require sergiors/routing-service-provider "dev-master"
```

How to user
-----------

Your yaml file
```yaml
index_controller:
    prefix: /
    defaults: {_controller: 'Acme\Acme\Controller\IndexController::indexAction'}
```

In your php file
```php
use Sergiors\Silex\RoutingServiceProvider;

$app->register(new RoutingServiceProvider(), [
    'routing.options' => [
        'paths' => __DIR__.'/routing.yml' // or an array
    ]
]);
```

To user `%root_dir%` in your yaml files, you need to install `sergiors/config-service-provider`.

License
-------
MIT
