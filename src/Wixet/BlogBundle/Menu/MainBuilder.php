<?php

namespace Wixet\BlogBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class MainBuilder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function topMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute("class", "menu");

        $menu->addChild('Inicio', array('route' => 'wixet_blog_default_index'));


        if($options["user"]){
            $menu->addChild('', array('route' => 'wixet_blog_default_index', 'extras' => array(
                'image' => $options["user"]->getProfileImageUrl()
            )));
            $menu['']->addChild('Salir', array('route' => 'fos_user_security_logout'));
        }
        // access services from the container!
        //$em = $this->container->get('doctrine')->getManager();
        //$locale = $this->container->get('request_stack')->getMasterRequest()->getLocale();
        // findMostRecent and Blog are just imaginary examples
        //$blog = $em->getRepository('WixetBlogBundle:BlogEntry')->findMostRecent($locale);

        /*$menu->addChild('Latest Blog Post', array(
            'route' => 'blog_show',
            'routeParameters' => array('id' => $blog->getId())
        ));*/

        // create another menu item
        /*$menu->addChild('About Me', array('route' => 'wixet_blog_default_index'));
        // you can also add sub level's to your menu's as follows
        $menu['About Me']->addChild('Edit profile', array('route' => 'wixet_blog_default_index'));
        $menu['About Me']->setChildrenAttribute("class", "sub-menu");
*/
        // ... add more children

        return $menu;
    }

}