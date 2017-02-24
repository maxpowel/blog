<?php

namespace Wixet\BlogBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Wixet\BlogBundle\Paginator\CommentableEntityInterface;


/**
 * BlogEntry
 *
 * @ORM\Table(name="blog_entry")
 * @ORM\Entity(repositoryClass="Wixet\BlogBundle\Repository\BlogEntryRepository")
 */
class BlogEntry implements CommentableEntityInterface
{
    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->translations = new ArrayCollection();
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $public;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $image;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $body;

    /**
     * @var Locale
     * @ORM\ManyToOne(targetEntity="Locale")
     */
    private $locale;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $title;

    /**
     * @ORM\ManyToMany(targetEntity="Tag")
     */
    private $tags;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="entry")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $author;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function getBodyPreview() {
        if(strpos($this->body, '<div style="page-break-after: always"><span style="display:none">&nbsp;</span></div>')) {
            return strstr($this->body, '<div style="page-break-after: always"><span style="display:none">&nbsp;</span></div>', true);
        } else {
            return $this->body;
        }

    }
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param mixed $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return Locale
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param Locale $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function __toString(){
        return $this->title;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return bool
     */
    public function isPublic()
    {
        return $this->public;
    }

    /**
     * @param bool $public
     */
    public function setPublic($public)
    {
        $this->public = $public;
    }

    private $numComments;
    public function setNumComments($num)
    {
        $this->numComments = $num;
    }

    public function getNumComments()
    {
        return $this->numComments;
    }


}

