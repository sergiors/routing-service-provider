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

        $app['routing.loader.xml'] = $app->factory(function (Container $app) {
            return new XmlFileLoader($app['routing.locator']);
        });

        $app['routing.loader.php'] = $app->factory(function (Container $app) {
            return new PhpFileLoader($app['routing.locator']);
        });

        $app['routing.loader.yml'] = $app->factory(function (Container $app) {
            return new YamlFileLoader($app, $app['routing.locator']);
        });

        $app['routing.loader.directory'] = $app->factory(function (Container $app) {
            return new DirectoryLoader($app['routing.locator']);
        });

        $app['routing.loader.resolver'] = function (Container $app) {
            $loaders = [
                $app['routing.loader.xml'],
                $app['routing.loader.php'],
                $app['routing.loader.directory'],
                $app['routing.loader.yml']
            ];

            return new LoaderResolver($loaders);
        };

        $app['routing.loader'] = function (Container $app) {
            return new DelegatingLoader($app['routing.loader.resolver']);
        };

        $app['routes'] = $app->extend('routes', function (RouteCollection $routes) use ($app) {
            $filenames = (array) $app['routing.filenames'];

            return array_reduce($filenames, function ($routes, $resource) use ($app) {
                $collection = $app['routing.loader']->load($resource);
                $routes->addCollection($collection);

                return $routes;
            }, $routes);
        });

        $app['routing.filenames'] = [];
    }
}
