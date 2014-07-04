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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Controller managing menus
 */
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
     * @param number $pageId
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
            $this->get('session')->getFlashBag()->add(
                    'notice',
                    $this->get('translator')->trans('fulgurio.lightcms.menus.moving_success_msg', array(), 'admin'));
        }
        return $this->redirect($this->generateUrl('AdminMenus'));
    }

    /**
     * Move up page position in menu
     *
     * @param number $pageId
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
        return $this->redirect($this->generateUrl('AdminMenus'));
    }

    /**
     * Change position of page in menu, in ajax
     *
     * @throws AccessDeniedException
     * @param number $pageId
     * @param string $menuName
     * @param number $newPosition
     * @return Response
     */
    public function changePositionAction($pageId, $menuName, $newPosition)
    {
        if ($this->getRequest()->isXmlHttpRequest())
        {
            $pageMenuRepo = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:PageMenu');
            $pageMenu = $pageMenuRepo->findOneBy(array('page' => $pageId, 'label' => $menuName));
            if ($pageMenu)
            {
                if ($newPosition == $pageMenu->getPosition())
                {
                    // No change
                }
                else if ($newPosition > $pageMenu->getPosition())
                {
                    if ($newPosition <= $pageMenuRepo->getLastMenuPosition($pageMenu->getLabel()))
                    {
                        // Down !
                        $pageMenuRepo->upMenuPagesPosition($pageMenu->getLabel(), $pageMenu->getPosition() + 1, $newPosition);
                        $pageMenu->setPosition($newPosition);
                        $em = $this->getDoctrine()->getEntityManager();
                        $em->persist($pageMenu);
                        $em->flush();
                    }
                }
                else if ($pageMenu->getPosition() > 1)
                {
                    // Up !
                    $pageMenuRepo->downMenuPagesPosition($pageMenu->getLabel(), $newPosition, $pageMenu->getPosition());
                    $pageMenu->setPosition($newPosition);
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($pageMenu);
                    $em->flush();
                }
                else {
                    // doesn t happen
                }
            }
            return new Response();
        }
        throw new AccessDeniedException();
    }
}