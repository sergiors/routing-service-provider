<?php

namespace Sergiors\Silex\Tests\Provider;

use Silex\Application;
use Silex\WebTestCase;
use Sergiors\Silex\Provider\ConfigServiceProvider;
use Sergiors\Silex\Provider\RoutingServiceProvider;

class RoutingServiceProviderTest extends WebTestCase
{
    /**
     * @test
     */
    public function register()
    {
        $app = $this->createApplication();
        $app->register(new ConfigServiceProvider(), [
            'config.replacements' => [
                'root_dir' => __DIR__,
            ],
        ]);
        $app->register(new RoutingServiceProvider(), [
            'routing.filename' => __DIR__.'/Resources/config/routing.yml'
        ]);

        $this->assertEquals(3, $app['routes']->count());
    }

    public function createApplication()
    {
        $app = new Application();
        $app['debug'] = true;

        return $app;
    }
}
