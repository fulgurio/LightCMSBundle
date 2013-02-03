<?php

namespace Fulgurio\LightCMSBundle\Controller;

use Fulgurio\LightCMSBundle\Controller\FrontPageController;

class FrontRedirectPageController extends FrontPageController
{
    /**
     * Redirection page
     */
    public function redirectAction()
    {
        return $this->redirect($this->page->getMetaValue('target_link'));
    }
}