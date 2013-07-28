<?php
/*
 * This file is part of the LightCMSBundle package.
 *
 * (c) Fulgurio <http://fulgurio.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fulgurio\LightCMSBundle\Controller;

use Fulgurio\LightCMSBundle\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Controller displaying front page
 */
class FrontPageController extends Controller
{
    /**
     * Page
     * @var Page
     */
    protected $page;


    /**
     * Display page
     *
     */
    public function showAction()
    {
        $models = $this->container->getParameter('fulgurio_light_cms.models');
        $templateName = isset($models[$this->page->getModel()]['front']['template']) ? $models[$this->page->getModel()]['front']['template'] : 'FulgurioLightCMSBundle:models:standardFront.html.twig';
        $pageRoot = $this->page->getSlug() == '' ? $this->page : $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:Page')->findOneByFullpath('');
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