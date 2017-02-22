<?php

namespace Wixet\BlogBundle\Paginator;


interface CommentableEntityInterface {
    /**
     * Set total number of comments
     * @param $num
     * @return mixed
     */
    public function setNumComments($num);
    public function getNumComments();

    /**
     * The unique identifier that will be used for find comments
     * ej: profiles/56, the slug for the profile page of the user 56
     * @return mixed
     */
    public function getSlug();
}