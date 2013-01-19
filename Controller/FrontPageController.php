<?php

namespace Fulgurio\LightCMSBundle\Controller;

use Fulgurio\LightCMSBundle\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontPageController extends Controller
{
    protected $page;

    /**
     * Display page
     *
     */
    public function showAction()
    {
        $pageRoot = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:Page')->findOneByFullpath('');
        return $this->render('FulgurioLightCMSBundle:FrontPage:standard.html.twig', array(
            'pageRoot' => $pageRoot,
            'currentPage' => $this->page
        ));
    }

    /**
     * $page setter
     * @param Page $page
     */
    final public function setPage(Page $page)
    {
        $this->page = $page;
    }
}