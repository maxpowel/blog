<?php
// src/AppBundle/Entity/User.php

namespace Wixet\BlogBundle\Entity;

use FOS\CommentBundle\Model\SignedCommentInterface;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $profileImageUrl;

    // Social identifiers
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $googleId;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $googleAccessToken;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $facebookId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $facebookAccessToken;

    /**
     * @return mixed
     */
    public function getGoogleId()
    {
        return $this->googleId;
    }

    /**
     * @param mixed $googleId
     */
    public function setGoogleId($googleId)
    {
        $this->googleId = $googleId;
        // Get profile image
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://picasaweb.google.com/data/entry/api/user/103977403080544158575?alt=json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($output,true);
        $imageUrl = $data["entry"]['gphoto$thumbnail']['$t'];
        $this->setProfileImageUrl($imageUrl);
    }

    /**
     * @return mixed
     */
    public function getGoogleAccessToken()
    {
        return $this->googleAccessToken;
    }

    /**
     * @param mixed $googleAccessToken
     */
    public function setGoogleAccessToken($googleAccessToken)
    {
        $this->googleAccessToken = $googleAccessToken;
    }

    /**
     * @return mixed
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * @param mixed $facebookId
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;
        $this->setProfileImageUrl("http://graph.facebook.com/v2.8/".$facebookId."/picture");
    }

    /**
     * @return mixed
     */
    public function getFacebookAccessToken()
    {
        return $this->facebookAccessToken;
    }

    /**
     * @param mixed $facebookAccessToken
     */
    public function setFacebookAccessToken($facebookAccessToken)
    {
        $this->facebookAccessToken = $facebookAccessToken;
    }

    /**
     * @return mixed
     */
    public function getProfileImageUrl()
    {
        return $this->profileImageUrl;
    }

    /**
     * @param mixed $profileImageUrl
     */
    public function setProfileImageUrl($profileImageUrl)
    {
        $this->profileImageUrl = $profileImageUrl;
    }
}
