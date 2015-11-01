<?php
namespace Sergiors\Silex\Provider;

use Silex\Application;
use Silex\WebTestCase;

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
                'root_dir' => __DIR__
            ]
        ]);
        $app->register(new RoutingServiceProvider(), [
            'router' => [
                'resource' => __DIR__.'/Resources/config/routing.yml'
            ]
        ]);

        $this->assertEquals(3, $app['routes']->count());
    }

    public function createApplication()
    {
        $app = new Application();
        $app['debug'] = true;
        $app['exception_handler']->disable();
        return $app;
    }
}
