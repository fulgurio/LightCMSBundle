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
     */
    public function setForm(Form $form)
    {
        $this->form = $form;
    }

    /**
     * $request setter
     *
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * $doctrine setter
     *
     * @param RegistryInterface $doctrine
     */
    public function setDoctrine(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * $user setter
     *
     * @param UserInterface $user
     */
    public function setUser(UserInterface$user)
    {
        $this->user = $user;
    }

    /**
     * $slugSuffixSeparator setter
     *
     * @param string $suffix
     */
    public function setSlugSuffixSeparator($suffix)
    {
        $this->slugSuffixSeparator = $suffix;
    }
}