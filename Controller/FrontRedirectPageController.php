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

use Fulgurio\LightCMSBundle\Controller\FrontPageController;

/**
 * Controller displaying redirect front page
 */
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