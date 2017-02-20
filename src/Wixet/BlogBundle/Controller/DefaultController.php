<?php

namespace Wixet\BlogBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Wixet\BlogBundle\Entity\BlogEntry;
use Wixet\BlogBundle\Entity\CommentThread;
use Wixet\BlogBundle\Repository\BlogEntryRepository;
use Wixet\BlogBundle\Service\BlogEntryService;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        $em = $this->getDoctrine()->getManager();
        //$article = $em->find('Entity\Article', 1 /*article id*/);
        /*$article->setTitle('my title in de');
        $article->setContent('my content in de');
        $article->setTranslatableLocale('de_de'); // change locale
        $em->persist($article);
        $em->flush();*/
        /**
         * @var $repo BlogEntryRepository
         */
        $repo = $em->getRepository("WixetBlogBundle:BlogEntry");
        $entries = array();
        foreach($repo->findMostRecent($request->getLocale()) as $entry){
            /**
             * @var $entry BlogEntry
             * @var $thread CommentThread
             */
            $thread = $this->get('fos_comment.manager.thread')->findThreadById($entry->getSlug());
            if (null === $thread) {
                $numComments = 0;
            } else {
                $numComments = $thread->getNumComments();
            }
            $entries[] = array(
                "entry" => $entry,
                "numComments" => $numComments
            );
        }



        return array("entries" => $entries);
    }
}
