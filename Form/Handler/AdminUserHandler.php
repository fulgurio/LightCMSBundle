<?php
/*
 * This file is part of the LightCMSBundle package.
 *
 * (c) Fulgurio <http://fulgurio.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fulgurio\LightCMSBundle\Form\Handler;

use Fulgurio\LightCMSBundle\Entity\User;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class AdminUserHandler
{
    /**
     * Form object
     * @var Form
     */
    protected $form;

    /**
     * Request data object
     * @var Request
     */
    protected $request;

    /**
     * Doctrine object
     * @var RegistryInterface
     */
    protected $doctrine;

    /**
     * Encoder factory
     * @var
     */
    protected $factory;


    /**
     * Constructor
     *
     * @param Form $form
     * @param Request $request
     * @param RegistryInterface $doctrine
     * @param EncoderFactoryInterface $factory
     */
    public function __construct(Form $form, Request $request, RegistryInterface $doctrine, EncoderFactoryInterface $factory)
    {
        $this->form = $form;
        $this->request = $request;
        $this->doctrine = $doctrine;
        $this->factory = $factory;
    }

    /**
     * Processing form values
     *
     * @param User $user
     * @return boolean
     */
    public function process(User $user)
    {
        if ($this->request->getMethod() === 'POST')
        {
            $clonedUser = clone $user;
            $this->form->handleRequest($this->request);
            if ($this->form->isValid())
            {
                $encoder = $this->factory->getEncoder($user);
                if (trim($user->getPassword()) != '')
                {
                    $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                    $user->setPassword($password);
                }
                else
                {
                    $user->setPassword($clonedUser->getPassword());
                }
                // New user
                if ($user->getId() == 0)
                {
                    $user->setCreatedAt(new \DateTime());
                }
                else
                {
                    $user->setUpdatedAt(new \DateTime());
                }
                $em = $this->doctrine->getManager();
                $em->persist($user);
                $em->flush();
                return TRUE;
            }
        }
        return FALSE;
    }
}