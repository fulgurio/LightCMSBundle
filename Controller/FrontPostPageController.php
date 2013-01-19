<?php

namespace Fulgurio\LightCMSBundle\Controller;

use Fulgurio\LightCMSBundle\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontPostPageController extends Controller
{
    /**
     * Display page
     *
     */
    public function listAction($pageNb)
    {
        $pageNb = intval((is_string($pageNb) && substr($pageNb, 0, 5) == 'page-') ? substr($pageNb, 5) : $pageNb + 1);
        --$pageNb;
        $pageRoot = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:Page')->findOneBy(array('fullpath' => ''));// @todo : mettre en service
        $config = $this->container->getParameter('fulgurio_light_cms.posts');
        $currentPage = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:Page')->findOneBy(array('fullpath' => $config['fullpath']));
        $pages = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:Page')->findBy(
                array('page_type' => 'post', 'status' => 'published'),
                array('created_at' => 'DESC'),
                $config['nb_per_page'],
                $config['nb_per_page'] * $pageNb);
        return $this->render('FulgurioLightCMSBundle:FrontPage:postsList.html.twig', array(
            'pageRoot' => $pageRoot,
            'currentPage' => $currentPage,
            'pages' => $pages,
        ));
    }
}