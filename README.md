Routing Service Provider
------------------------
Import your routes from yaml, php files or directory.

Install
-------
```
composer require inbep/routing-service-provider
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
$app->register(new RoutingServiceProvider(), [
    'router' => [
        'resource' => __DIR__.'/routing.yml'
    ]
]);
```

To user `%root_dir%` in your yaml files, you need to install `inbep/config-service-provider`.

License
-------
MIT
