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

use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractAdminHandler
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
     * User object
     * @var UserInterface
     */
    protected $user;

    /**
     * Slug suffix separator, if slug already exist
     * @var string
     */
    protected $slugSuffixSeparator = '-';


    /**
     * $form setter
     *
     * @param Form $form
     * @return AbstractAdminHandler
     */
    public function setForm(Form $form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * $form getter
     *
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * $request setter
     *
     * @param Request $request
     * @return AbstractAdminHandler
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * $request getter
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * $doctrine setter
     *
     * @param RegistryInterface $doctrine
     * @return AbstractAdminHandler
     */
    public function setDoctrine(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;

        return $this;
    }

    /**
     * $doctrine getter
     *
     * @return RegistryInterface
     */
    public function getDoctrine()
    {
        return $this->doctrine;
    }

    /**
     * $user setter
     *
     * @param UserInterface $user
     * @return AbstractAdminHandler
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * $user getter
     *
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * $slugSuffixSeparator setter
     *
     * @param string $suffix
     * @return AbstractAdminHandler
     */
    public function setSlugSuffixSeparator($suffix)
    {
        $this->slugSuffixSeparator = $suffix;

        return $this;
    }

    /**
     * $slugSuffixSeparator getter
     *
     * @return string
     */
    public function getSlugSuffixSeparator()
    {
        return $this->slugSuffixSeparator;
    }
}