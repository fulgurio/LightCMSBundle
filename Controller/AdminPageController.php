<?php

namespace Fulgurio\LightCMSBundle\Controller;

use Fulgurio\LightCMSBundle\Entity\Page;
use Fulgurio\LightCMSBundle\Form\AdminPageHandler;
use Fulgurio\LightCMSBundle\Form\AdminPageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class AdminPageController extends Controller
{
    /**
     * Pages list
     */
    public function listAction()
    {
        $pageRoot = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:Page')->findOneBy(array('fullpath' => '', 'page_type' => 'page'));
        return $this->render('FulgurioLightCMSBundle:AdminPage:list.html.twig', array(
            'pageRoot' => array($pageRoot)
        ));
    }

    /**
     * Page manager page
     *
     * @param integer $pageId
     */
    public function selectAction($pageId)
    {
        $models = $this->container->getParameter('fulgurio_light_cms.models');
        $pageRoot = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:Page')->findOneBy(array('fullpath' => '', 'page_type' => 'page'));
        $page = $this->getPage($pageId);
        $templateName = isset($models[$page->getModel()]['back']['view']) ? $models[$page->getModel()]['back']['view'] : 'FulgurioLightCMSBundle:models:standardAdminView.html.twig';
        return $this->render($templateName, array(
            'pageRoot' => array($pageRoot),
            'selectedPage' => $page
        ));
    }

    /**
     * Add page
     *
     * @param integer $parentId if specified, we edit a (new) child page
     */
    public function addAction($parentId)
    {
        $models = $this->container->getParameter('fulgurio_light_cms.models');
        $page = new Page();
        $parent = $this->getPage($parentId);
        if (!$models[$parent->getModel()]['allow_childrens'])
        {
            throw new AccessDeniedException();
        }
        $page->setParent($parent);
        return $this->createPage($page, array('parent' => $parent));
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
            'pageMetas' => $this->getPageMetas($pageId)
        );
        $page = $this->getPage($pageId);
        return $this->createPage($page, $options);
    }

    /**
     * Create form for page entity, use for edit or add page
     *
     * @param Page $page
     * @param array $options
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    private function createPage($page, $options)
    {
        $models = $this->container->getParameter('fulgurio_light_cms.models');
        if ($this->get('request')->getMethod() == 'POST')
        {
            $data = $this->get('request')->get('page');
            $page->setModel($data['model']);
        }
        $formClassName = isset($models[$page->getModel()]['back']['form']) ? $models[$page->getModel()]['back']['form'] : '\Fulgurio\LightCMSBundle\Form\AdminPageType';
        $formHandlerClassName = isset($models[$page->getModel()]['back']['handler']) ? $models[$page->getModel()]['back']['handler'] : '\Fulgurio\LightCMSBundle\Form\AdminPageHandler';
        $form = $this->createForm(new $formClassName($this->container), $page);
        $formHandler = new $formHandlerClassName();
        $formHandler->setForm($form);
        $formHandler->setRequest($this->get('request'));
        $formHandler->setDoctrine($this->getDoctrine());
        if ($formHandler->process($page))
        {
            $this->get('session')->setFlash(
                    'success',
                    $this->get('translator')->trans(
                            isset($options['pageId']) ? 'fulgurio.lightcms.pages.edit_form.success_msg' : 'fulgurio.lightcms.pages.add_form.success_msg',
                            array(),
                            'admin'
                    )
            );
            return $this->redirect($this->generateUrl('AdminPagesSelect', array('pageId' => $page->getId())));
        }
        $options['form'] = $form->createView();
        $options['tiny_mce'] = $this->container->getParameter('fulgurio_light_cms.tiny_mce');
        $templateName = isset($models[$page->getModel()]['back']['template']) ? $models[$page->getModel()]['back']['template'] : 'FulgurioLightCMSBundle:models:standardAdminAddForm.html.twig';
        return $this->render($templateName, $options);
    }

    /**
     * Remove page, with confirm form
     *
     * @param integer $pageId
     */
    public function removeAction($pageId)
    {
        $page = $this->getPage($pageId);
        if ($page->getSlug() == '')
        {
            throw new AccessDeniedException();
        }
        $request = $this->container->get('request');
        if ($request->request->get('confirm') === 'yes')
        {
            if ($this->container->getParameter('fulgurio_light_cms.allow_recursive_page_delete') == FALSE
                    && $page->hasChildren())
            {
                $this->get('session')->setFlash(
                        'error',
                        $this->get('translator')->trans('fulgurio.lightcms.pages.recursive_remove_not_allowed', array(), 'admin')
                );
                return new RedirectResponse($this->generateUrl('AdminPagesSelect', array('pageId' => $pageId)));
            }
            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($page);
            $em->flush();
            return new RedirectResponse($this->generateUrl('AdminPages'));
        }
        else if ($request->request->get('confirm') === 'no')
        {
            return new RedirectResponse($this->generateUrl('AdminPagesSelect', array('pageId' => $pageId)));
        }
        $templateName = $request->isXmlHttpRequest() ? 'FulgurioLightCMSBundle::adminConfirmAjax.html.twig' : 'FulgurioLightCMSBundle::adminConfirm.html.twig';
        return $this->render($templateName, array(
            'action' => $this->generateUrl('AdminPagesRemove', array('pageId' => $pageId)),
            'confirmationMessage' => $this->get('translator')->trans('fulgurio.lightcms.pages.delete_confirm_message', array('%title%' => $page->getTitle()), 'admin'),
        ));
    }

    /**
     * Change page position using ajax tree
     *
     * @return type
     * @throws AccessDeniedException
     */
    public function changePositionAction()
    {
        $request = $this->get('request');
        if ($request->isXmlHttpRequest())
        {
            $em = $this->getDoctrine()->getEntityManager();
            $pageRepository = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:Page');
            $page = $this->getPage($request->request->get('pageId'));
            $parentId = $request->request->get('parentId');
            $position = $request->request->get('position');
            if ($parentId != $page->getParent()->getId())
            {
                $pageRepository->downPagesPosition($parentId, $position);
                $pageRepository->upPagesPosition($page->getParent(), $page->getPosition() + 1);
                $page->setParent($pageRepository->find($parentId));
                $models = $this->container->getParameter('fulgurio_light_cms.models');
                $formHandlerClassName = isset($models[$page->getModel()]['back']['handler']) ? $models[$page->getModel()]['back']['handler'] : '\Fulgurio\LightCMSBundle\Form\AdminPageHandler';
                $formHandler = new $formHandlerClassName();
                $formHandler->setDoctrine($this->getDoctrine());
                $formHandler->makeFullpath($page);
                $em->persist($page);
                $em->flush();
            }
            else
            {
                if ($position > $page->getPosition())
                {
                    $pageRepository->upPagesPosition($page->getParent(), $page->getPosition() + 1, $position - 1);
                    --$position;
                }
                else
                {
                    $pageRepository->downPagesPosition($page->getParent(), $position, $page->getPosition() - 1);
                }
            }
            $page->setPosition($position);
            $em->persist($page);
            $em->flush();
            $response = new Response(json_encode(array('code' => 42)));
            $response->headers->set('Content-Type', 'application/json');
            return ($response);
        }
        throw new AccessDeniedException();
    }

    /**
     * Get page from given ID, and ckeck if it exists
     *
     * @param integer $pageId
     * @throws NotFoundHttpException
     */
    private function getPage($pageId)
    {
        if (!$page = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:Page')->findOneBy(array('id' => $pageId, 'page_type' => 'page')))
        {
            throw new NotFoundHttpException(
                $this->get('translator')->trans('fulgurio.lightcms.page_not_found')
            );
        }
        return ($page);
    }

    /**
     * Get meta data from given ID page
     *
     * @param integer $pageId
     * @return array
     */
    private function getPageMetas($pageId)
    {
        $pageMetas = array();
        $metas = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:PageMeta')->findByPage($pageId);
        foreach ($metas as $meta)
        {
            $pageMetas[$meta->getMetaKey()] = $meta;
        }
        return ($pageMetas);
    }
}