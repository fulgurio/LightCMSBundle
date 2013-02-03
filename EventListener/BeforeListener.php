<?php
namespace Fulgurio\LightCMSBundle\EventListener;

use Fulgurio\LightCMSBundle\Controller\FrontPageController;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\SecurityContext;

class BeforeListener
{
    private $doctrine;

    /**
     * @var \AppKernel
     */
    private $kernel;
    private $page;

    /**
     * @var SecurityContext
     */
    private $securityContext;


    /**
     * Constructor
     *
     * @param RegistryInterface $doctrine
     * @param $kernel
     * @param SecurityContext $securityContext
     */
    public function __construct(RegistryInterface $doctrine, \AppKernel $kernel, SecurityContext $securityContext)
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
                if (!($this->page = $this->doctrine->getRepository('FulgurioLightCMSBundle:Page')->findOneBy(array('fullpath' => $fullpath))))
                {
                    throw new NotFoundHttpException();
                }
                if ($this->page->getStatus() === 'draft' && !(
                         $this->securityContext->isGranted('ROLE_ADMIN')
                         && !is_null($event->getRequest()->server->get('HTTP_REFERER'))
                ))
                {
                    throw new NotFoundHttpException();
                }
                //@todo : check access
                $models = $this->kernel->getContainer()->getParameter('fulgurio_light_cms.models');
                if (isset($models[$this->page->getModel()]['front']['controller']))
                {
                    $arr = $this->getController($models[$this->page->getModel()]['front']['controller']);
                    $event->setController($arr);
                }
                if (method_exists($controller[0], 'setPage'))
                {
                    $controller[0]->setPage($this->page);
                }
            }
        }
    }

    private function getController($controller)
    {
        list($controller, $method) = $this->createController($controller);
        if (!method_exists($controller, $method))
        {
            throw new \InvalidArgumentException(sprintf('Method "%s::%s" does not exist.', get_class($controller), $method));
        }
        return array($controller, $method);
    }

    /**
     * Returns a callable for the given controller.
     *
     * @param string $controller A Controller string
     *
     * @return mixed A PHP callable
     */
    private function createController($controller)
    {
        if (false === strpos($controller, '::'))
        {
            $count = substr_count($controller, ':');
            if (2 == $count)
            {
                // controller in the a:b:c notation then
                $controller = $this->parser->parse($controller);
            }
            elseif (1 == $count)
            {
                // controller in the service:method notation
                list($service, $method) = explode(':', $controller, 2);
                return array($this->kernel->getContainer()->get($service), $method);
            }
            else
            {
                throw new \LogicException(sprintf('Unable to parse the controller name "%s".', $controller));
            }
        }
        list($class, $method) = explode('::', $controller, 2);
        $method .= 'Action';
        if (!class_exists($class))
        {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }
        if (!method_exists($class, $method))
        {
            throw new \InvalidArgumentException(sprintf('Method "%s" of "%s" class does not exist.', $method, $class));
        }
        $controller = new $class();
        if ($controller instanceof \Fulgurio\LightCMSBundle\Controller\FrontPageController)
        {
            $controller->setContainer($this->kernel->getContainer());
            $controller->setPage($this->page);
        }
        return array($controller, $method);
    }
}