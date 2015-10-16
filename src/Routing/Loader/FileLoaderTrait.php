<?php
namespace Inbep\Silex\Routing\Loader;

use Symfony\Component\Config\FileLocatorInterface;

/**
 * @author Sérgio Rafael Siqueira <sergio@inbep.com.br>
 */
trait FileLoaderTrait
{
    /**
     * @var \Pimple
     */
    protected $container;

    /**
     * Constructor.
     *
     * @param \Pimple $container A Pimple instance
     * @param FileLocatorInterface $locator A FileLocator instance
     */
    public function __construct(\Pimple $container, FileLocatorInterface $locator)
    {
        $this->container = $container;

        parent::__construct($locator);
    }
}
