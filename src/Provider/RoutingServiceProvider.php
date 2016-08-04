<?php

namespace Sergiors\Silex\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\Loader\XmlFileLoader;
use Symfony\Component\Routing\Loader\PhpFileLoader;
use Symfony\Component\Routing\Loader\DirectoryLoader;
use Sergiors\Silex\Routing\ChainUrlMatcher;
use Sergiors\Silex\Routing\ChainUrlGenerator;
use Sergiors\Silex\Routing\Loader\YamlFileLoader;

/**
 * @author Sérgio Rafael Siqueira <sergio@inbep.com.br>
 */
class RoutingServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['request_matcher_class'] = 'Silex\\Provider\\Routing\\RedirectableUrlMatcher';

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
                $app['routing.loader.directory']
            ];

            if (class_exists('Symfony\\Component\\Yaml\\Yaml')) {
                $loaders[] = $app['routing.loader.yml'];
            }

            return new LoaderResolver($loaders);
        };

        $app['routing.loader'] = function (Container $app) {
            return new DelegatingLoader($app['routing.loader.resolver']);
        };

        $app['router'] = function (Container $app) {
            $options = [
                'debug' => $app['debug'],
                'cache_dir' => $app['routing.cache_dir'],
                'matcher_base_class' => $app['request_matcher_class'],
                'matcher_class' => $app['request_matcher_class']
            ];

            return new Router(
                $app['routing.loader'],
                $app['routing.resource'],
                $options,
                $app['request_context'],
                $app['logger']
            );
        };

        $app['request_matcher'] = $app->extend('request_matcher', function ($matcher, $app) {
            $matchers = [$app['router'], $matcher];
            return new ChainUrlMatcher($matchers, $app['request_context']);
        });

        $app['url_generator'] = $app->extend('url_generator', function ($generator, $app) {
            $generators = [$app['router'], $generator];
            return new ChainUrlGenerator($generators, $app['request_context']);
        });

        $app['routing.resource'] = null;
        $app['routing.cache_dir'] = null;
    }
}
