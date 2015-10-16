Install
-------
```
composer require inbep/routing-service-provider
```

```php
$app->register(new RoutingServiceProvider(), [
    'router' => [
        'resource' => __DIR__.'/routing.yml'
    ]
]);
```

License
-------
MIT
