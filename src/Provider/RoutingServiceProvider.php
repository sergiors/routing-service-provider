<?php
namespace Inbep\Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
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
        if (!isset($app['file_locator'])) {
            throw new \LogicException('You must register the ConfigServiceProvider to use the RoutingFilterServiceProvider');
        }

        $app['routing.resource'] = null;

        $app['routing.loader.xml'] = $app->share(function (Application $app) {
            return new XmlFileLoader($app['file_locator']);
        });

        $app['routing.loader.php'] = $app->share(function (Application $app) {
            return new PhpFileLoader($app['file_locator']);
        });

        $app['routing.loader.yml'] = $app->share(function (Application $app) {
            return new YamlFileLoader($app['file_locator']);
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
                $collection = $app['routing.loader']->load($app['routing.resource']);
                $routes->addCollection($collection);
                return $routes;
            })
        );
    }

    public function boot(Application $app)
    {
    }
}
