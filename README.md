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
    'routing.resource' => __DIR__.'/routing.yml'
]);
```
Remeber, you need install `symfony/yaml` to use YAML.

To use `%root_dir%` in your yaml files, you need to install `symfony/dependency-injection` and set `$app['routing.replacements'] = ['root_dir' => '']`

License
-------
MIT
