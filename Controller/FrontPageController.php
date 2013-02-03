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
        $models = $this->container->getParameter('fulgurio_light_cms.models');
        $templateName = isset($models[$this->page->getModel()]['front']['template']) ? $models[$this->page->getModel()]['front']['template'] : 'FulgurioLightCMSBundle:models:standardFront.html.twig';
        return $this->render($templateName, array(
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