<?php
namespace Fulgurio\LightCMSBundle\Extension;

use Fulgurio\LightCMSBundle\Entity\Page;

class LightCMSTwigExtension extends \Twig_Extension
{
    /**
     * (non-PHPdoc)
     * @see Twig_Extension::getFunctions()
     */
    public function getFunctions()
    {
        return array(
            'dataForBreadcrumb' =>        new \Twig_Function_Method($this, 'getDataForBreadcrumb')
        );
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