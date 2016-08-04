<?php

namespace Sergiors\Silex\Routing\Loader;

use Pimple\Container;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Routing\Loader\YamlFileLoader as BaseYamlFileLoader;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser as YamlParser;

class YamlFileLoader extends BaseYamlFileLoader implements FileLoaderInterface
{
    protected $container;

    private $yamlParser;

    public function __construct(Container $container, FileLocatorInterface $locator)
    {
        $this->container = $container;

        parent::__construct($locator);
    }

    public function load($file, $type = null)
    {
        $path = $this->locator->locate($file);

        if (!class_exists('Symfony\\Component\\Yaml\\Parser')) {
            throw new \RuntimeException(
                'Unable to load YAML config files as the Symfony Yaml Component is not installed.'
            );
        }

        if (!stream_is_local($path)) {
            throw new \InvalidArgumentException(sprintf('This is not a local file "%s".', $path));
        }

        if (!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf('File "%s" not found.', $path));
        }

        if (null === $this->yamlParser) {
            $this->yamlParser = new YamlParser();
        }

        try {
            $parsedConfig = $this->yamlParser->parse(file_get_contents($path));
        } catch (ParseException $e) {
            throw new \InvalidArgumentException(sprintf('The file "%s" does not contain valid YAML.', $path), 0, $e);
        }

        if (isset($this->container['config.replacements.resolver'])) {
            $parsedConfig = $this->container['config.replacements.resolver']($parsedConfig);
        }

        $collection = new RouteCollection();
        $collection->addResource(new FileResource($path));

        // empty file
        if (null === $parsedConfig) {
            return $collection;
        }

        // not an array
        if (!is_array($parsedConfig)) {
            throw new \InvalidArgumentException(sprintf('The file "%s" must contain a YAML array.', $path));
        }

        foreach ($parsedConfig as $name => $config) {
            $this->validate($config, $name, $path);

            if (isset($config['resource'])) {
                $this->parseImport($collection, $config, $path, $file);
            } else {
                $this->parseRoute($collection, $name, $config, $path);
            }
        }

        return $collection;
    }
}
