<?php

namespace Wixet\BlogBundle\Block;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;


use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;

class TagsBlockService extends AbstractBlockService
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct($type, $templating, $em, RequestStack $requestStack)
    {
        parent::__construct($type, $templating);
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'template' => 'WixetBlogBundle:Block:tags.html.twig'
        ));
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        // merge settings
        $settings = $blockContext->getSettings();

        $query = $this->em->createQuery("SELECT t FROM WixetBlogBundle:Tag t JOIN t.locale l WHERE l.locale LIKE :locale");
        $query->setParameter("locale", $this->requestStack->getMasterRequest()->getLocale()."%");




        return $this->renderResponse($blockContext->getTemplate(), array(
            'tags'=> $query->getResult(),
            'block'     => $blockContext->getBlock(),
            'settings'  => $settings
        ), $response);
    }
}
