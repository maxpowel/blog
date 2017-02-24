<?php

namespace Wixet\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Wixet\BlogBundle\Entity\BlogEntry;

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

        $query = $em->createQuery("SELECT b FROM WixetBlogBundle:BlogEntry b JOIN b.locale l JOIN b.category c WHERE b.public=true");
        foreach($query->getResult() as $entry){
            /**
             * @var $entry BlogEntry
             */
            $locale = substr($entry->getLocale()->getLocale(), 0, 2);
            $urls[] = array(
                'loc' => $this->get('router')->generate($locale.'__RG__wixet_blog_entry_entry',
                    array('slug' => $entry->getSlug(), 'category' => $entry->getCategory())
                ),
                'changefreq' => 'monthly',
                'priority' => '0.5'
            );
        }

        return $this->render('WixetBlogBundle:Sitemap:sitemap.xml.twig', array(
            'urls'     => $urls,
            'hostname' => $hostname
        ));
    }
}