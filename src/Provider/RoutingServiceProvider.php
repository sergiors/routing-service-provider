<?php
namespace Inbep\Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Loader\XmlFileLoader;
use Symfony\Component\Routing\Loader\PhpFileLoader;
use Symfony\Component\Routing\Loader\YamlFileLoader;

/**
 * @author Sérgio Rafael Siqueira <sergio@inbep.com.br>
 */
class RoutingServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['routing.resources'] = [];

        $app['routing.loader.xml'] = $app->share(function () {
            return new XmlFileLoader(new FileLocator());
        });

        $app['routing.loader.php'] = $app->share(function () {
            return new PhpFileLoader(new FileLocator());
        });

        $app['routing.loader.yml'] = $app->share(function () {
            return new YamlFileLoader(new FileLocator());
        });

        $app['routing.resolver'] = $app->share(function (Application $app) {
            $loaders = [
                $app['routing.loader.xml'],
                $app['routing.loader.php']
            ];

            if (class_exists('Symfony\Component\Yaml\Yaml')) {
                $loaders[] = $app['routing.loader.yml'];
            }

            return new LoaderResolver($loaders);
        });

        $app['routing.loader'] = $app->share(function (Application $app) {
            return new DelegatingLoader($app['routing.resolver']);
        });

        $app['routes'] = $app->share(
            $app->extend('routes', function (RouteCollection $routes) use ($app) {
                $resources = (array) $app['routing.resources'];
                foreach ($resources as $resource) {
                    $collection = $app['routing.loader']->load($resource);
                    $routes->addCollection($collection);
                }
                return $routes;
            })
        );
    }

    public function boot(Application $app)
    {
    }
}
