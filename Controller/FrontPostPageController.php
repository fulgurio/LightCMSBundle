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

use Fulgurio\LightCMSBundle\Controller\FrontPageController;

/**
 * Controller displaying post
 */
class FrontPostPageController extends FrontPageController
{
    /**
     * Display page
     * @todo : pagination
     */
    public function listAction()
    {
//         $pageNb = intval((is_string($pageNb) && substr($pageNb, 0, 5) == 'page-') ? substr($pageNb, 5) : $pageNb + 1);
//         --$pageNb;
        $pageNb = 0;
        $pageRoot = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:Page')->findOneBy(array('fullpath' => ''));// @todo : mettre en service
        $config = $this->container->getParameter('fulgurio_light_cms.posts');
        $currentPage = $this->page;
        $nbPerPage = $this->page->getMetaValue('nb_posts_per_page') ? $this->page->getMetaValue('nb_posts_per_page') : $config['nb_per_page'];
        $pages = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:Page')->findBy(
                array('page_type' => 'post', 'status' => 'published'),
                array('created_at' => 'DESC'),
                $nbPerPage,
                $nbPerPage * $pageNb);
        return $this->render('FulgurioLightCMSBundle:models:postsListFront.html.twig', array(
            'pageRoot' => $pageRoot,
            'currentPage' => $this->page,
            'pages' => $pages,
        ));
    }
}