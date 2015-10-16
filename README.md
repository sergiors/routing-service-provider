RoutingServiceProvider
----------------------
Import your routes from YAML, PHP files or Directory.

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

To user `%root_dir%` in your YAML files, yo need yo install `inbep/config-service-provider`.

License
-------
MIT
