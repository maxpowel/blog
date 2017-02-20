<?php

namespace Wixet\BlogBundle\Twig;

use Symfony\Component\HttpFoundation\RequestStack;

class BlogExtension extends \Twig_Extension
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function setRequest(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;

    }
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('ldate', array($this, 'lDate'), array('needs_environment' => true)),
        );
    }

    public function lDate(\Twig_Environment $env, $date)
    {
        return twig_localized_date_filter($env, $date, 'full', 'none', $this->requestStack->getMasterRequest()->getLocale());
    }

    public function getName()
    {
        return 'blog_extension';
    }
}