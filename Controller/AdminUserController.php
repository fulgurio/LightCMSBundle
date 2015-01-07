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

use Fulgurio\LightCMSBundle\Entity\User;
use Fulgurio\LightCMSBundle\Form\Handler\AdminUserHandler;
use Fulgurio\LightCMSBundle\Form\Type\AdminUserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Controller managing users
 */
class AdminUserController extends Controller
{
    /**
     * Users list
     */
    public function listAction()
    {
        $this->checkConfiguration();
        $filters = array();
        $nbPerPage = 10;
        $request = $this->getRequest();
        $page = $request->get('page') > 1 ? $request->get('page') - 1 : 0;
        $usersNb = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:User')->count($filters);
        $users = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:User')->findAllWithPagination($filters, $nbPerPage, $page * $nbPerPage);
        return $this->render('FulgurioLightCMSBundle:AdminUser:list.html.twig', array(
                'users' => $users,
                'nbUsers' => $usersNb,
                'pageCount' => ceil($usersNb / $nbPerPage),
                'current' => $page + 1,
                'route' => 'AdminUsers',
                'query' => array()
        ));
    }

    /**
     * Add new user
     */
    public function addAction()
    {
        $this->checkConfiguration();
        $user = new User();
        return $this->createUser($user);
    }

    /**
     * Edit existing user
     *
     * @param number $userId
     */
    public function editAction($userId)
    {
        $this->checkConfiguration();
        $user = $this->getSpecifiedUser($userId);
        return $this->createUser($user);
    }

    /**
     * Create user form
     *
     * @param User $user
     * @return  \Symfony\Component\HttpFoundation\Response
     */
    private function createUser(User $user)
    {
        $options = array();
        if ($user->getId() > 0)
        {
            $options['userId'] = $user->getId();
        }
        $form = $this->createForm(new AdminUserType($this->container), $user);
        $formHandler = new AdminUserHandler(
                $form,
                $this->getRequest(),
                $this->getDoctrine(),
                $this->container->get('security.encoder_factory')
        );
        if ($formHandler->process($user))
        {
            $this->get('session')->getFlashBag()->add(
                    'success',
                    $this->get('translator')->trans(
                            isset($options['userId']) ? 'fulgurio.lightcms.users.edit_form.success_msg' : 'fulgurio.lightcms.users.add_form.success_msg',
                            array(),
                            'admin'
                    )
            );
            return $this->redirect($this->generateUrl('AdminUsers'));
        }
        $options['form'] = $form->createView();
        return $this->render('FulgurioLightCMSBundle:AdminUser:add.html.twig', $options);
    }

    /**
     * Remove user, with confirm form
     *
     * @param number $userId
     */
    public function removeAction($userId)
    {
        $this->checkConfiguration();
        $user = $this->getSpecifiedUser($userId);
        if ($user->getId() == $this->getUser()->getId())
        {
            throw new AccessDeniedException($this->get('translator')->trans('fulgurio.lightcms.users.current_user_deletion_error', array(), 'admin'));
        }
        $request = $this->getRequest();
        if ($request->request->get('confirm') === 'yes')
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                    'success',
                    $this->get('translator')->trans('fulgurio.lightcms.users.delete_success_message', array(), 'admin')
            );
            return $this->redirect($this->generateUrl('AdminUsers'));
        }
        else if ($request->request->get('confirm') === 'no')
        {
            return $this->redirect($this->generateUrl('AdminUsers'));
        }
        $templateName = $request->isXmlHttpRequest() ? 'FulgurioLightCMSBundle::adminConfirmAjax.html.twig' : 'FulgurioLightCMSBundle::adminConfirm.html.twig';
        return $this->render($templateName, array(
                'action' => $this->generateUrl('AdminUsersRemove', array('userId' => $userId)),
                'confirmationMessage' => $this->get('translator')->trans('fulgurio.lightcms.users.delete_confirm_message', array('%username%' => $user->getUsername()), 'admin'),
        ));
    }

    /**
     * Check if configuration allows users manager access
     *
     * @throws AccessDeniedException
     */
    private function checkConfiguration()
    {
        $currentUser = $this->getUser();
        if (get_class($currentUser) !== 'Fulgurio\LightCMSBundle\Entity\User')
        {
            throw new AccessDeniedException(
                    $this->get('translator')->trans('fulgurio.lightcms.users.not_available', array(), 'admin')
            );
        }
    }

    /**
     * Get user from given ID, and ckeck if it exists
     *
     * @param number $userId
     * @return Fulgurio\LightCMSBundle\Entity\User
     * @throws NotFoundHttpException
     */
    private function getSpecifiedUser($userId)
    {
        if (!$user = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:User')->findOneBy(array('id' => $userId)))
        {
            throw new NotFoundHttpException(
                    $this->get('translator')->trans('fulgurio.lightcms.users.not_found', array(), 'admin')
            );
        }
        return $user;
    }
}