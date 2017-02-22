<?php

namespace Wixet\BlogBundle\Paginator;

use Doctrine\ORM\Query;
use FOS\CommentBundle\Model\ThreadManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Knp\Component\Pager\Event\ItemsEvent;

class PaginateBlogEntrySubscriber implements EventSubscriberInterface
{

    /**
     * @var ThreadManager
     */
    private $threadManager;

    public function setThreadManager(ThreadManager $threadManager) {
        $this->threadManager = $threadManager;
    }

    public function items(ItemsEvent $event)
    {
        if(isset($event->options["comment_info"]) and $event->options["comment_info"]) {
            /**
             * @var $query Query
             */
            $query = $event->target;
            $query->setMaxResults($event->getLimit());
            $query->setFirstResult($event->getOffset());
            $items = $query->getResult();
            foreach($items as $element) {
                /**
                 * @var $element CommentableEntityInterface
                 */
                $thread = $this->threadManager->findThreadById($element->getSlug());
                if (null === $thread) {
                    $element->setNumComments(0);
                } else {
                    $element->setNumComments($thread->getNumComments());
                }
            }
            $event->items = $items;
            $event->stopPropagation();
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            'knp_pager.items' => array('items', 1)
        );
    }

}