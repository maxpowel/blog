<?php

namespace Wixet\BlogBundle\Block;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;


use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;

class CategoriesBlockService extends AbstractBlockService
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
            'template' => 'WixetBlogBundle:Block:categories.html.twig'
        ));
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        // merge settings
        $settings = $blockContext->getSettings();

        $query = $this->em->createQuery("SELECT count(c.id) as total, c.name FROM WixetBlogBundle:Category c JOIN c.locale l JOIN c.entry e WHERE l.locale LIKE :locale GROUP BY c.id");
        $query->setParameter("locale", $this->requestStack->getMasterRequest()->getLocale()."%");
        $categories = $query->getResult();



        return $this->renderResponse($blockContext->getTemplate(), array(
            'categories'=> $categories,
            'block'     => $blockContext->getBlock(),
            'settings'  => $settings
        ), $response);
    }
}
