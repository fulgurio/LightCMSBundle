<?php
namespace Fulgurio\LightCMSBundle\Form;

use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * Get upload file dir
     *
     * @throws \Exception
     */
    protected function getUploadDir()
    {
        $dir = __DIR__ . '/../../../../web/';
        if (!is_dir($dir))
        {
            $dir = __DIR__ . '/../../../../../web';
            if (!is_dir($dir))
            {
                $dir = __DIR__ . '/../../../../../../web';
                if (!is_dir($dir))
                {
                    throw new \Exception('Upload dir not found');
                }
            }
        }
        if (!is_dir($dir . $this->getUploadUrl()))
        {
            if (!is_writable($dir))
            {
                throw new \Exception($dir . ' is not writable');
            }
            else
            {
                mkdir($dir . $this->getUploadUrl());
            }
        }
        return ($dir . $this->getUploadUrl());
    }

    /**
     * Upload path url
     *
     * @return string
     */
    protected function getUploadUrl()
    {
        return ('/uploads/');
    }

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
}