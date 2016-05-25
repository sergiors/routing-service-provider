<?php

namespace Sergiors\Silex\Routing\Loader;

use Pimple\Container;
use Symfony\Component\Config\FileLocatorInterface;

interface FileLoaderInterface
{
    /**
     * Constructor.
     *
     * @param \Pimple              $container A Pimple instance
     * @param FileLocatorInterface $locator   A FileLocator instance
     */
    public function __construct(Container $container, FileLocatorInterface $locator);
}
