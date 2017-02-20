<?php

namespace Wixet\BlogBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Wixet\BlogBundle\Entity\BlogEntry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Wixet\BlogBundle\Entity\BlogEntryTranslation;

class EntryController extends Controller
{
    /**
     * @Route("/{category}/{slug}")
     * @Template
     * @ParamConverter("entry", options={"mapping": {"slug": "slug"}})
     */
    public function entryAction(BlogEntry $entry, Request $request)
    {

        $id = $entry->getSlug();
        $thread = $this->get('fos_comment.manager.thread')->findThreadById($id);
        if (null === $thread) {
            $thread = $this->get('fos_comment.manager.thread')->createThread();
            $thread->setId($id);
            $thread->setPermalink($request->getUri());

            // Add the thread
            $this->get('fos_comment.manager.thread')->saveThread($thread);
        }

        $comments = $this->get('fos_comment.manager.comment')->findCommentTreeByThread($thread);



        return array(
            "entry" => $entry,
            'comments' => $comments,
            'thread' => $thread
        );
    }
}
