<?php

namespace Wixet\BlogBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Wixet\RecaptchaBundle\Form\Type\WixetRecaptchaType;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("recaptcha", WixetRecaptchaType::class, array(
            'explicit_render' => true
        ));
        
    }

    public function getParent()
    {
        return 'FOS\CommentBundle\Form\CommentType';
    }

}