<?php

namespace Wixet\BlogBundle\Service;


use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;

class BlogEntryService
{

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(RequestStack $requestStack, EntityManager $em) {
        $this->requestStack = $requestStack;
        $this->em = $em;
    }

    public function findLocalized() {
        $locale = $this->requestStack->getMasterRequest()->getLocale();
        if(!$locale){
            $locale = $this->requestStack->getMasterRequest()->getDefaultLocale();
        }
        $query = $this->em->createQuery("SELECT e FROM WixetBlogBundle:BlogEntry e JOIN e.locale l WHERE l.locale LIKE :locale ORDER BY e.createdAt DESC");
        $query->setParameter("locale", $locale."%");
        return $query->getResult();
    }
}

