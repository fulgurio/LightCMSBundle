<?php

namespace Fulgurio\LightCMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class AdminMenuController extends Controller
{
    /**
     * Pages list
     */
    public function listAction()
    {
        $menus = $this->container->getParameter('fulgurio_light_cms.menus');
        $pageMenuRepo = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:PageMenu');
        $pages = array();
        foreach ($menus as $menu)
        {
            $pages[$menu] = $pageMenuRepo->findPagesOfMenu($menu);
        }
        return $this->render('FulgurioLightCMSBundle:AdminMenu:list.html.twig', array(
            'menuPages' => $pages
        ));
    }

    /**
     * Move up page position in menu
     *
     * @param intger $pageId
     * @param string $menuName
     * @return
     */
    public function upAction($pageId, $menuName)
    {
        $pageMenuRepo = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:PageMenu');
        $pageMenu = $pageMenuRepo->findOneBy(array('page' => $pageId, 'label' => $menuName));
        $position = $pageMenu->getPosition();
        if ($position > 1)
        {
            $pageMenuRepo->downMenuPagesPosition($menuName, $position - 1, $position);
            $pageMenu->setPosition($position - 1);
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($pageMenu);
            $em->flush();
            $this->get('session')->setFlash('notice', $this->get('translator')->trans('fulgurio.lightcms.menus.moving_success_msg', array(), 'admin'));
        }
        return (new RedirectResponse($this->generateUrl('AdminMenus')));
    }

    /**
     * Move up page position in menu
     *
     * @param intger $pageId
     * @param string $menuName
     * @return
     */
    public function downAction($pageId, $menuName)
    {
        $pageMenuRepo = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:PageMenu');
        $pageMenu = $pageMenuRepo->findOneBy(array('page' => $pageId, 'label' => $menuName));
        $position = $pageMenu->getPosition();
        if ($position < $pageMenuRepo->getLastMenuPosition($menuName))
        {
            $pageMenuRepo->upMenuPagesPosition($menuName, $position + 1, $position + 1);
            $pageMenu->setPosition($position + 1);
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($pageMenu);
            $em->flush();
            $this->get('session')->setFlash('notice', $this->get('translator')->trans('fulgurio.lightcms.menus.moving_success_msg', array(), 'admin'));
        }
        return (new RedirectResponse($this->generateUrl('AdminMenus')));
    }
}