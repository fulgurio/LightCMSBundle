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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Controller managing pages
 */
class AdminPageController extends Controller
{
    /**
     * Pages list
     *
     * @param number $pageId
     */
    public function listAction($pageId = NULL)
    {
        $pages = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:Page')->findBy(array('page_type' => 'page'), array('position' => 'ASC'));
        $childrenPages = array();
        foreach ($pages as $page)
        {
            if ($page->isRoot())
            {
                $pageRoot = $page;
            }
            if (!is_null($pageId) && $page->getId() == $pageId)
            {
                $currentPage = $page;
            }
            if (!isset($childrenPages[$page->getParentId()]))
            {
                $childrenPages[$page->getParentId()] = array();
            }
            $childrenPages[$page->getParentId()][] = $page;
        }
        $data = array(
            'pageRoot' => array($pageRoot),
            'childrenPages' => $childrenPages
        );
        if (!is_null($pageId))
        {
            if (!isset($currentPage))
            {
                throw new NotFoundHttpException($this->get('translator')->trans('fulgurio.lightcms.page_not_found', array(), 'admin'));
            }
            $models = $this->container->getParameter('fulgurio_light_cms.models');
            $templateName = isset($models[$currentPage->getModel()]['back']['view']) ? $models[$currentPage->getModel()]['back']['view'] : 'FulgurioLightCMSBundle:models:standardAdminView.html.twig';
            $data['selectedPage'] = $currentPage;
        }
        else
        {
            $templateName = 'FulgurioLightCMSBundle:AdminPage:list.html.twig';
        }
        return $this->render($templateName, $data);
    }

    /**
     * Add page
     *
     * @param number $parentId if specified, we edit a (new) child page
     */
    public function addAction($parentId)
    {
        $models = $this->container->getParameter('fulgurio_light_cms.models');
        // We remove models if it s unique and there's one in database
        foreach ($models as $modelName => $model)
        {
            if ($model['is_unique'])
            {
                $em = $this->getDoctrine()->getManager();
                if ($em->getRepository('FulgurioLightCMSBundle:Page')->findOneBy(array('model' => $modelName)))
                {
                    unset($models[$modelName]);
                }
            }
        }
        $page = new Page();
        $parent = $this->getPage($parentId);
        if (!$models[$parent->getModel()]['allow_children'])
        {
            throw new AccessDeniedException();
        }
        $page->setParent($parent);
        return $this->createPage(
                $page,
                array('parent' => $parent),
                $models
        );
    }

    /**
     * Edit page
     *
     * @param number $pageId if specified, we are on edit page form
     */
    public function editAction($pageId)
    {
        $options = array(
            'pageId' => $pageId,
            'pageMetas' => $this->getPageMetas($pageId)
        );
        $page = $this->getPage($pageId);
        return $this->createPage(
                $page,
                $options,
                $models = $this->container->getParameter('fulgurio_light_cms.models')
        );
    }

    /**
     * Create form for page entity, use for edit or add page
     *
     * @param Page $page
     * @param array $options
     * @param array $models
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    private function createPage($page, $options, $models)
    {
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST')
        {
            $data = $request->get('page');
            $page->setModel($data['model']);
        }
        $formClassName = isset($models[$page->getModel()]['back']['form']) ? $models[$page->getModel()]['back']['form'] : '\Fulgurio\LightCMSBundle\Form\Type\AdminPageType';
        $formHandlerClassName = isset($models[$page->getModel()]['back']['handler']) ? $models[$page->getModel()]['back']['handler'] : '\Fulgurio\LightCMSBundle\Form\Handler\AdminPageHandler';
        $formType = new $formClassName($this->container);
        $formType->setModels($models);
        $form = $this->createForm($formType, $page);
        $formHandler = new $formHandlerClassName();
        $formHandler->setForm($form)
                ->setRequest($request)
                ->setDoctrine($this->getDoctrine())
                ->setSlugSuffixSeparator($this->container->getParameter('fulgurio_light_cms.slug_suffix_separator'));
        if ($this->getUser()) {
            $formHandler->setUser($this->getUser());
        }
        if ($formHandler->process($page))
        {
            $this->get('session')->getFlashBag()->add(
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
        if ($this->container->getParameter('fulgurio_light_cms.wysiwyg')
                && $this->container->hasParameter($this->container->getParameter('fulgurio_light_cms.wysiwyg')))
        {
            $options['wysiwyg'] = $this->container->getParameter($this->container->getParameter('fulgurio_light_cms.wysiwyg'));
        }
        if ($page->getParent()&& $page->getParent()->getMetaValue('is_home')
                && $this->container->hasParameter('fulgurio_light_cms.languages'))
        {
            $options['useMultiLang'] = TRUE;
        }
        $templateName = isset($models[$page->getModel()]['back']['template']) ? $models[$page->getModel()]['back']['template'] : 'FulgurioLightCMSBundle:models:standardAdminAddForm.html.twig';
        return $this->render($templateName, $options);
    }

    /**
     * Remove page, with confirm form
     *
     * @param number $pageId
     */
    public function removeAction($pageId)
    {
        $page = $this->getPage($pageId);
        if ($page->getSlug() == '')
        {
            throw new AccessDeniedException();
        }
        $request = $this->getRequest();
        if ($request->request->get('confirm') === 'yes')
        {
            if ($this->container->getParameter('fulgurio_light_cms.allow_recursive_page_delete') == FALSE
                    && $page->hasChildren())
            {
                $this->get('session')->getFlashBag()->add(
                        'error',
                        $this->get('translator')->trans('fulgurio.lightcms.pages.recursive_remove_not_allowed', array(), 'admin')
                );
                return $this->redirect($this->generateUrl('AdminPagesSelect', array('pageId' => $pageId)));
            }
            $em = $this->getDoctrine()->getManager();

            $em->remove($page);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                    'success',
                    $this->get('translator')->trans('fulgurio.lightcms.pages.delete_success_message', array(), 'admin')
            );
            if ($page->getParentId())
            {
                return $this->redirect($this->generateUrl('AdminPagesSelect', array('pageId' => $page->getParentId())));
            }
            return $this->redirect($this->generateUrl('AdminPages'));
        }
        else if ($request->request->get('confirm') === 'no')
        {
            return $this->redirect($this->generateUrl('AdminPagesSelect', array('pageId' => $pageId)));
        }
        $templateName = $request->isXmlHttpRequest() ? 'FulgurioLightCMSBundle::adminConfirmAjax.html.twig' : 'FulgurioLightCMSBundle::adminConfirm.html.twig';
        return $this->render($templateName, array(
            'action' => $this->generateUrl('AdminPagesRemove', array('pageId' => $pageId)),
            'confirmationMessage' => $this->get('translator')->trans('fulgurio.lightcms.pages.delete_confirm_message', array('%title%' => $page->getTitle()), 'admin'),
        ));
    }

    /**
     * Page copy, use for multilang site
     *
     * @param number $sourceId
     * @param number $targetId
     * @param string $lang
     * @return \Symfony\Component\HttpFoundation\Response>
     */
    public function copyAction($sourceId, $targetId, $lang)
    {
        $em = $this->getDoctrine()->getManager();
        $source = $this->getPage($sourceId);
        $target = $this->getPage($targetId);
        $newPage = clone($source);
        $newPage->setParent($target);
        // Children page
        if ($target->getLang())
        {
            $newPage->setLang($target->getLang());
        }
        else
        {
            $newPage->setLang($lang);
            $newPage->setPosition($em->getRepository('FulgurioLightCMSBundle:Page')->getNextPosition($target));
        }
        $newPage->setStatus('draft');
        $newPage->setSourceId($sourceId);
        $models = $this->container->getParameter('fulgurio_light_cms.models');
        return $this->createPage($newPage, array('pageMetas' => $this->getPageMetas($sourceId), 'parent' => $target, 'sourceId' => $sourceId), $models);
    }

    /**
     * Change page position using ajax tree
     *
     * @return type
     * @throws AccessDeniedException
     */
    public function changePositionAction()
    {
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest())
        {
            $em = $this->getDoctrine()->getManager();
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
                $formHandlerClassName = isset($models[$page->getModel()]['back']['handler']) ? $models[$page->getModel()]['back']['handler'] : '\Fulgurio\LightCMSBundle\Form\Handler\AdminPageHandler';
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
            return $response;
        }
        throw new AccessDeniedException();
    }

    /**
     * Get page from given ID, and ckeck if it exists
     *
     * @param number $pageId
     * @throws NotFoundHttpException
     */
    private function getPage($pageId)
    {
        if (!$page = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:Page')->findOneBy(array('id' => $pageId, 'page_type' => 'page')))
        {
            throw new NotFoundHttpException(
                $this->get('translator')->trans('fulgurio.lightcms.page_not_found', array(), 'admin')
            );
        }
        return $page;
    }

    /**
     * Get meta data from given ID page
     *
     * @param number $pageId
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
        return $pageMetas;
    }
}