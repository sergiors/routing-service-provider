<?php

namespace Sergiors\Silex\Routing;

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

class ChainUrlMatcher implements UrlMatcherInterface, RequestMatcherInterface
{
    /**
     * @var RequestMatcherInterface[]
     */
    protected $matchers = [];

    /**
     * @var RequestContext
     */
    protected $context;

    public function __construct(array $matchers, RequestContext $context)
    {
        array_walk($matchers, [$this, 'add']);
        $this->context = $context;
    }

    /**
     * @param UrlMatcherInterface $matcher
     */
    public function add(UrlMatcherInterface $matcher)
    {
        $this->matchers[] = $matcher;
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
    public function match($pathinfo)
    {
        return $this->doMatch($pathinfo);
    }

    /**
     * {@inheritdoc}
     */
    public function matchRequest(Request $request)
    {
        return $this->doMatch($request->getPathInfo(), $request);
    }

    protected function doMatch($pathinfo, Request $request = null)
    {
        $throw = null;

        foreach ($this->matchers as $matcher) {
            try {
                $matcher->setContext($this->context);

                if ($request && $matcher instanceof RequestMatcherInterface) {
                    return $matcher->matchRequest($request);
                }

                return $matcher->match($pathinfo);
            } catch (\Exception $e) {
                $throw = $e;
            }
        }

        throw $throw;
    }
}
