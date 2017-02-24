<?php

namespace Wixet\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SitemapController extends Controller
{
    public function sitemapAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $urls = array();
        $hostname = $request->getSchemeAndHttpHost();

        // incluye url página inicial
        $urls[] = array(
            'loc' => $this->get('router')->generate('es__RG__wixet_blog_default_index'),
            'changefreq' => 'weekly',
            'priority' => '1.0'
        );

        $urls[] = array(
            'loc' => $this->get('router')->generate('en__RG__wixet_blog_default_index'),
            'changefreq' => 'weekly',
            'priority' => '1.0'
        );


        return $this->render('WixetBlogBundle:Sitemap:sitemap.xml.twig', array(
            'urls'     => $urls,
            'hostname' => $hostname
        ));
    }
}