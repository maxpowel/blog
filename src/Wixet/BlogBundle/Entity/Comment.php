<?php

namespace Wixet\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\CommentBundle\Entity\Comment as BaseComment;
use FOS\CommentBundle\Model\SignedCommentInterface;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @ORM\Entity
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @ORM\Table("entry_comment")
 */
class Comment extends BaseComment implements SignedCommentInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Thread of this comment
     *
     * @var CommentThread
     * @ORM\ManyToOne(targetEntity="Wixet\BlogBundle\Entity\CommentThread")
     */
    protected $thread;

    public $recaptcha;

    /**
     * Author of the comment
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @var User
     */
    protected $author;

    public function setAuthor(UserInterface $author)
    {
        $this->author = $author;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getAuthorName()
    {
        if (null === $this->getAuthor()) {
            return 'Anonymous';
        }

        return $this->getAuthor()->getName();
    }

    public function getAuthorImage()
    {
        if (null === $this->getAuthor()) {
            $identicon = new \Identicon\Identicon();
            return $identicon->getImageDataUri($this->getCreatedAt()->getTimestamp());
        }

        return $this->getAuthor()->getProfileImageUrl();
    }
}