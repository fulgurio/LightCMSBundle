<?php

namespace Fulgurio\LightCMSBundle\Controller;

use Fulgurio\LightCMSBundle\Entity\UploadedFile;
use Fulgurio\LightCMSBundle\Form\FileBrowserForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminUploadFileController extends Controller
{
    /**
     * TinyMCE upload file manager
     */
    public function tinyMCEBrowserAction()
    {
        $request = $this->get('request');
        $file = new UploadedFile();
        $form = $this->createForm(new FileBrowserForm(), $file);
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);
            if ($form->isValid())
            {
                return $this->render('FulgurioLightCMSBundle:AdminPage:filesBrowser_success.html.twig', array(
                    'fileUrl' => $file->upload(),
                ));
            }
        }
        return $this->render('FulgurioLightCMSBundle:AdminPage:filesBrowser.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}