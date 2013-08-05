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
     */
    public function listAction()
    {
        // @todo : may be put it as service
        $pageRoot = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:Page')->findOneBy(array('fullpath' => ''));
        $config = $this->container->getParameter('fulgurio_light_cms.posts');
        $currentPage = $this->get('request')->query->get('page', 1);
        $nbPerPage = $this->page->getMetaValue('nb_posts_per_page') ? $this->page->getMetaValue('nb_posts_per_page') : $config['nb_per_page'];
        $posts = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:Page')->findAllPublishedPosts(
                $this->get('knp_paginator'),
                $currentPage,
                $nbPerPage
        );
        return $this->render('FulgurioLightCMSBundle:models:postsListFront.html.twig', array(
            'pageRoot' => $pageRoot,
            'currentPage' => $this->page,
            'posts' => $posts,
        ));
    }
}