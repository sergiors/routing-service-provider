<?php

namespace Sergiors\Silex\Routing\Loader;

use Symfony\Component\Config\FileLocatorInterface;

use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Loader\XmlFileLoader as BaseXmlFileLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class XmlFileLoader extends BaseXmlFileLoader
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

     /**
     * @param FileLocatorInterface $locator A FileLocatorInterface instance
     * @param ParameterBagInterface|null $parameterBag
     */
    public function __construct(FileLocatorInterface $locator, ParameterBagInterface $parameterBag = null)
    {
        parent::__construct($locator);

        $this->parameterBag = $parameterBag;
    }

    /**
     * Loads an XML file.
     *
     * @param string      $file An XML file path
     * @param string|null $type The resource type
     *
     * @return RouteCollection A RouteCollection instance
     *
     * @throws \InvalidArgumentException When the file cannot be loaded or when the XML cannot be
     *                                   parsed because it does not validate against the scheme.
     */
    public function load($file, $type = null)
    {
        $path = $this->locator->locate($file);

        $xml = $this->loadFile($path);

        $collection = new RouteCollection();
        $collection->addResource(new FileResource($path));

        // process routes and imports
        foreach ($xml->documentElement->childNodes as $node) {
            if (!$node instanceof \DOMElement) {
                continue;
            }

            if ($this->parameterBag
                && $resource = $node->getAttribute('resource')
            ) {
                $node->setAttribute('resource', $this->parameterBag->resolveValue($resource));
            }

            $this->parseNode($collection, $node, $path, $file);
        }

        return $collection;
    }
}
