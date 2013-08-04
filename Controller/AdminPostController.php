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
use Fulgurio\LightCMSBundle\Form\AdminPostHandler;
use Fulgurio\LightCMSBundle\Form\AdminPostType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Controller managing posts
 */
class AdminPostController extends Controller
{
    /**
     * Posts list
     */
    public function listAction()
    {
        //@todo: pagination
        $pageNb = $this->get('request')->query->get('page', 1);
        try
        {
            $parent = $this->getPostsListPage();
        }
        catch (\Exception $e)
        {
             $parent = NULL;
        }
        $posts = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:Page')->findAllPosts($this->get('knp_paginator'), $pageNb);
        return $this->render('FulgurioLightCMSBundle:AdminPost:list.html.twig',
        array(
            'posts' => $posts,
            'hasNoPostRoot' => is_null($parent)
        ));
    }

    /**
     * Add post
     */
    public function addAction()
    {
        $post = new Page();
        $parent = $this->getPostsListPage();
        $post->setParent($parent);
        return $this->createPage($post, array());
    }

    /**
     * Edit page
     *
     * @param integer $pageId if specified, we are on edit page form
     */
    function editAction($pageId)
    {
        $options = array(
            'pageId' => $pageId,
        );
        $post = $this->getPost($pageId);
        return $this->createPage($post, $options);
    }

    /**
     * Create form for page entity, use for edit or add page
     *
     * @param Page $page
     * @param array $options
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    private function createPage(Page $post, $options)
    {
        $form = $this->createForm(new AdminPostType(), $post);
        $formHandler = new AdminPostHandler();
        $formHandler->setForm($form);
        $formHandler->setRequest($this->get('request'));
        $formHandler->setDoctrine($this->getDoctrine());
        $formHandler->setSlugSuffixSeparator($this->container->getParameter('fulgurio_light_cms.slug_suffix_separator'));
        $formHandler->setPostConfig($this->container->getParameter('fulgurio_light_cms.posts'));
        if ($formHandler->process($post))
        {
            $this->get('session')->setFlash(
                    'success',
                    $this->get('translator')->trans(
                            isset($options['pageId']) ? 'fulgurio.lightcms.posts.edit_form.success_msg' : 'fulgurio.lightcms.posts.add_form.success_msg',
                            array(),
                            'admin'
                    )
            );
            return $this->redirect($this->generateUrl('AdminPosts'));
        }
        $options['form'] = $form->createView();
        $options['tiny_mce'] = $this->container->getParameter('fulgurio_light_cms.tiny_mce');
        return $this->render('FulgurioLightCMSBundle:models:postAdminAddForm.html.twig', $options);
    }

    /**
     * Remove page, with confirm form
     *
     * @param integer $pageId
     */
    public function removeAction($pageId)
    {
        $post = $this->getPost($pageId);
        $request = $this->container->get('request');
        if ($request->request->get('confirm') === 'yes')
        {
            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($post);
            $em->flush();
            return new RedirectResponse($this->generateUrl('AdminPosts'));
        }
        else if ($request->request->get('confirm') === 'no')
        {
            return new RedirectResponse($this->generateUrl('AdminPosts'));
        }
        $templateName = $request->isXmlHttpRequest() ? 'FulgurioLightCMSBundle::adminConfirmAjax.html.twig' : 'FulgurioLightCMSBundle::adminConfirm.html.twig';
        return $this->render($templateName, array(
            'action' => $this->generateUrl('AdminPostsRemove', array('pageId' => $pageId)),
            'confirmationMessage' => $this->get('translator')->trans('fulgurio.lightcms.posts.delete_confirm_message', array('%title%' => $post->getTitle()), 'admin'),
        ));
    }

    /**
     * Get posts list page
     *
     * @return Page
     * @throws NotFoundHttpException
     */
    private function getPostsListPage()
    {
        if (!$page = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:Page')->findOneBy(array('model' => 'postsList', 'page_type' => 'page')))
        {
            throw new NotFoundHttpException(
                $this->get('translator')->trans('fulgurio.lightcms.posts.posts_list_page_not_found', array(), 'admin')
            );
        }
        return ($page);
    }

    /**
     * Get page from given ID, and ckeck if it exists
     *
     * @param integer $pageId
     * @throws NotFoundHttpException
     */
    private function getPost($pageId)
    {
        if (!$page = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:Page')->findOneBy(array('id' => $pageId, 'page_type' => 'post')))
        {
            throw new NotFoundHttpException(
                $this->get('translator')->trans('fulgurio.lightcms.post_not_found')
            );
        }
        return ($page);
    }
}