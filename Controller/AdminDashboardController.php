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

/**
 * Controller displaying dashboard
 */
class AdminDashboardController extends Controller
{
    /**
     * Dashboard
     */
    public function indexAction()
    {
        return $this->render('FulgurioLightCMSBundle::adminBase.html.twig');
    }
}