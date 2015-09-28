Install
-------
```
composer require inbep/routing-service-provider
```

```php
$app->register(use Inbep\Silex\Provider\RoutingServiceProvider());
$app['routing.resources'] = __DIR__.'/routing.yml'; // or an array
```

License
-------
MIT
