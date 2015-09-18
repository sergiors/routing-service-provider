<?php
namespace Inbep\Silex\Provider;

use Silex\Application;
use Silex\WebTestCase;
use Inbep\Silex\Provider\ConfigServiceProvider;

class RoutingServiceProviderTest extends WebTestCase
{
    /**
     * @test
     */
    public function register()
    {
        $app = $this->createApplication();
        $app->register(new RoutingServiceProvider());
    }

    public function createApplication()
    {
        $app = new Application();
        $app['debug'] = true;
        $app['exception_handler']->disable();
        return $app;
    }
}