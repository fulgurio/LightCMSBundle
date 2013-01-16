<?php

namespace Fulgurio\LightCMSBundle\Controller;

use Fulgurio\LightCMSBundle\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContext;

class FrontPageController extends Controller
{
    protected $slug;

    protected $page;

    protected $pageMetas = array();

    /**
     * Display page
     *
     * @throws NotFoundHttpException
     * @throws AccessDeniedException
     */
    public function showAction()
    {
        $request = $this->container->get('request');
        return $this->render('FulgurioLightCMSBundle:FrontPage:standard.html.twig', array(
            'page' => $this->page,
        ));
    }

    /**
     * $page setter
     * @param Page $page
     */
    final public function setPage(Page $page)
    {
        $this->page = $page;
    }

    /**
     * Page metas setter
     * @param array of \Fulgurio\LightCMSBundle\Entity\PageMeta $data
     */
    final public function setPageMetas($data)
    {
        foreach ($data as $meta)
        {
            $this->pageMetas[$meta->getMetaKey()] = $meta->getMetaValue();
        }
    }
}