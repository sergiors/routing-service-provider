<?php

namespace Sergiors\Silex\Routing;

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ChainUrlGenerator implements UrlGeneratorInterface
{
    /**
     * @var UrlGeneratorInterface[]
     */
    protected $generators = [];

    /**
     * @var RequestContext
     */
    protected $context;

    public function __construct(array $generators, RequestContext $context)
    {
        array_walk($generators, [$this, 'add']);
        $this->context = $context;
    }

    /**
     * @param UrlGeneratorInterface $generator
     */
    public function add(UrlGeneratorInterface $generator)
    {
        $this->generators[] = $generator;
    }

    /**
     * {@inheritdoc}
     */
    public function setContext(RequestContext $context)
    {
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH)
    {
        $throw = null;

        foreach ($this->generators as $generator) {
            $generator->setContext($this->context);

            try {
                return $generator->generate($name, $parameters, $referenceType);
            } catch (\Exception $e) {
                $throw = $e;
            }
        }

        throw $throw;
    }
}
