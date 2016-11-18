<?php

namespace Sergiors\Silex\Tests\Provider;

use Silex\Application;
use Silex\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
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
            'routing.cache_dir' => sys_get_temp_dir(),
            'routing.resource' => __DIR__.'/Resources/config/routing.yml'
        ]);

        $app->match('/hello')->bind('hello');

        $request = Request::create('/');
        $app->handle($request);

        $this->assertEquals('/hello', $app['url_generator']->generate('hello'));
        $this->assertEquals('/fake', $app['url_generator']->generate('fake'));
        $this->assertEquals('/test_import/import', $app['url_generator']->generate('test_import'));
    }

    public function createApplication()
    {
        return new Application();
    }
}
