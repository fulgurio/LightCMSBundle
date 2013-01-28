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
     * Constructor
     *
     * @param UrlGeneratorInterface $generator
     */
    public function __construct(UrlGeneratorInterface $generator)
    {
    	$this->generator = $generator;
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

    /**
     * (non-PHPdoc)
     * @see Twig_ExtensionInterface::getName()
     */
    public function getName()
    {
    	return 'LightCMS_extension';
    }
}