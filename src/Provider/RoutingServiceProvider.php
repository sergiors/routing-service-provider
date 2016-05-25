<?php

namespace Sergiors\Silex\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Loader\XmlFileLoader;
use Symfony\Component\Routing\Loader\PhpFileLoader;
use Symfony\Component\Routing\Loader\DirectoryLoader;
use Sergiors\Silex\Routing\Loader\YamlFileLoader;

/**
 * @author Sérgio Rafael Siqueira <sergio@inbep.com.br>
 */
class RoutingServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['routing.locator'] = function () {
            return new FileLocator();
        };

        $app['routing.loader.xml'] = function () use ($app) {
            return new XmlFileLoader($app['routing.locator']);
        };

        $app['routing.loader.php'] = function () use ($app) {
            return new PhpFileLoader($app['routing.locator']);
        };

        $app['routing.loader.yml'] = function () use ($app) {
            return new YamlFileLoader($app, $app['routing.locator']);
        };

        $app['routing.loader.directory'] = function () use ($app) {
            return new DirectoryLoader($app['routing.locator']);
        };

        $app['routing.resolver'] = function () use ($app) {
            $loaders = [
                $app['routing.loader.xml'],
                $app['routing.loader.php'],
                $app['routing.loader.directory'],
                $app['routing.loader.yml']
            ];

            return new LoaderResolver($loaders);
        };

        $app['routing.loader'] =function () use ($app) {
            return new DelegatingLoader($app['routing.resolver']);
        };

        $app['routes'] = $app->extend('routes', function (RouteCollection $routes) use ($app) {
            $paths = (array) $app['routing.options']['paths'];

            return array_reduce($paths, function ($routes, $resource) use ($app) {
                $collection = $app['routing.loader']->load($resource);
                $routes->addCollection($collection);

                return $routes;
            }, $routes);
        });

        $app['routing.options'] = [
            'paths' => [],
        ];
    }
}
