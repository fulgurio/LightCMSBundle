<?php

namespace Fulgurio\LightCMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminDashboardController extends Controller
{
    /**
     * Pages list
     */
    public function indexAction()
    {
        return $this->render('FulgurioLightCMSBundle::adminBase.html.twig');
    }
}