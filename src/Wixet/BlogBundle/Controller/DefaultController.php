<?php

namespace Wixet\BlogBundle\Controller;

use Doctrine\ORM\Query;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Wixet\BlogBundle\Entity\Category;
use Wixet\BlogBundle\Service\BlogEntryService;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Wixet\BlogBundle\Entity\BlogEntry;
use Wixet\BlogBundle\Entity\CommentThread;
use Wixet\BlogBundle\Entity\Tag;
use Wixet\BlogBundle\Repository\BlogEntryRepository;


class DefaultController extends Controller
{
    /**
     * @Route("/tag/{slug}")
     * @Template
     * @ParamConverter("tag", options={"mapping": {"slug": "slug"}})
     * 
     * @param Tag $tag
     * @param Request $request
     * @return array
     */
    public function tagListAction(Tag $tag, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /**
         * @var $repo BlogEntryRepository
         */
        $repo = $em->getRepository("WixetBlogBundle:BlogEntry");

        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         */
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $repo->findMostRecentQuery($request->getLocale(), null, $tag),
            $request->get("page", 1),
            $request->get("size", 10),
            array("comment_info" => true)
        );

        return array("blogEntryPagination" => $pagination, "tag" => $tag);
    }

    /**
     * @Route("/{slug}")
     * @Template
     * @ParamConverter("category", options={"mapping": {"slug": "slug"}})
     *
     * @param Category $category
     * @param Request $request
     * @return array
     */
    public function categoryListAction(Category $category, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /**
         * @var $repo BlogEntryRepository
         */
        $repo = $em->getRepository("WixetBlogBundle:BlogEntry");

        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         */
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $repo->findMostRecentQuery($request->getLocale(), $category),
            $request->get("page", 1),
            $request->get("size", 10),
            array("comment_info" => true)
        );

        return array("blogEntryPagination" => $pagination, "category" => $category);
    }

    /**
     * @Route("/")
     * @Template
     *
     * @param Request $request
     * @return array
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /**
         * @var $repo BlogEntryRepository
         */
        $repo = $em->getRepository("WixetBlogBundle:BlogEntry");

        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         */
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $repo->findMostRecentQuery($request->getLocale()),
            $request->get("page", 1),
            $request->get("size", 10),
            array("comment_info" => true)
        );

        return array("blogEntryPagination" => $pagination);
    }
}
