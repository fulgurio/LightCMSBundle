<?php
namespace Fulgurio\LightCMSBundle\Extension;

use Fulgurio\LightCMSBundle\Entity\Page;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LightCMSTwigExtension extends \Twig_Extension
{
    /**
     * Routing generator object
     *
     * @var UrlGeneratorInterface
     */
    private $generator;

    /**
     * Container
     *
     * @var unknown
     */
    private $container;


    /**
     * Constructor
     *
     * @param UrlGeneratorInterface $generator
     */
    public function __construct(UrlGeneratorInterface $generator, $container)
    {
        $this->generator = $generator;
        $this->container = $container;
    }

    /**
     * (non-PHPdoc)
     * @see Twig_Extension::getFunctions()
     */
    public function getFunctions()
    {
        return array(
            'dataForBreadcrumb' => new \Twig_Function_Method($this, 'getDataForBreadcrumb'),
            'pagePath'          => new \Twig_Function_Method($this, 'getPagePath'),
            'allowChildrens'    => new \Twig_Function_Method($this, 'allowChildrens'),
        );
    }

    /**
     * Get path, like path twig extension, but more extensible (for futur)
     *
     * @param Page $page
     * @return string
     */
    public function getPagePath(Page $page)
    {
        return $this->generator->generate('LightCMS_Page', array('fullpath' => $page->getFullpath()), false);
    }

    /**
     * Get breadcrumb data
     *
     * @param Page $page
     * @return array|NULL
     */
    public function getDataForBreadcrumb(Page $page)
    {
        $parent = $page->getParent();
        if ($parent)
        {
            $data = array();
            if ($parent->getParent())
            {
                $data = array_merge($this->getDataForBreadcrumb($parent), $data);
            }
            $data[] = $parent;
            return $data;
        }
        return NULL;
    }

    public function allowChildrens(Page $page)
    {
        $models = $this->container->getParameter('fulgurio_light_cms.models');
        return ($models[$page->getModel()]['allow_childrens']);
    }

    /**
     * (non-PHPdoc)
     * @see Twig_ExtensionInterface::getName()
     */
    public function getName()
    {
        return 'LightCMS_extension';
    }
}