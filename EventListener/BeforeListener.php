<?php
namespace Fulgurio\LightCMSBundle\EventListener;

use Fulgurio\LightCMSBundle\Controller\FrontPageController;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BeforeListener
{
    private $doctrine;
    private $kernel;
    private $page;
    private $securityContext;


    /**
     * Constructor
     *
     * @param RegistryInterface $doctrine
     * @param $kernel
     * @$param $securityContext
     */
    public function __construct(RegistryInterface $doctrine, $kernel, $securityContext)
    {
        $this->doctrine = $doctrine;
        $this->kernel = $kernel;
        $this->securityContext = $securityContext;
    }

    /**
     * OnKernelControler event filter
     * Try to find the page into database.
     *
     * @param FilterControllerEvent $event
     * @throws NotFoundHttpException
     * @throws AccessDeniedHttpException
     * @return void|\Fulgurio\LightCMSBundle\EventListener\RedirectResponse
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        /**
         * $controller passed can be either a class or a Closure. This is not usual in Symfony2 but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller))
        {
            return;
        }
        $fullpath = $event->getRequest()->get('fullpath');
        if ($fullpath != '' && $fullpath[mb_strlen($fullpath) - 1] == '/')
        {
            $fullpath = mb_substr($fullpath, 0, -1);
        }
        if (isset($controller[0]))
        {
            if ($controller[0] instanceof FrontPageController)
            {
                if (!($this->page = $this->doctrine->getRepository('FulgurioLightCMSBundle:Page')->findOneByFullpath($fullpath)))
                {
                    throw new NotFoundHttpException();
                }
                if ($this->page->getStatus() == 'draft')
                {
                    throw new NotFoundHttpException();
                }
                if (method_exists($controller[0], 'setPage'))
                {
                    $controller[0]->setPage($this->page);
                }
            }
        }
    }
}