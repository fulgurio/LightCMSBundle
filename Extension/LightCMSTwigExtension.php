<?php
namespace Fulgurio\LightCMSBundle\Extension;

use Fulgurio\LightCMSBundle\Entity\Page;
use Fulgurio\LightCMSBundle\Utils\LightCMSUtils;
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
     * Doctrine object
     *
     * @var UrlGeneratorInterface
     */
    private $doctrine;

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
    public function __construct(UrlGeneratorInterface $generator, $doctrine, $container)
    {
        $this->generator = $generator;
        $this->doctrine  = $doctrine;
        $this->container = $container;
    }

    /**
     * (non-PHPdoc)
     * @see Twig_Extension::getFunctions()
     */
    public function getFunctions()
    {
        return array(
            'dataForBreadcrumb'   => new \Twig_Function_Method($this, 'getDataForBreadcrumb'),
            'pagePath'            => new \Twig_Function_Method($this, 'getPagePath'),
            'allowChildrens'      => new \Twig_Function_Method($this, 'allowChildrens'),
            'needTranslatedPages' => new \Twig_Function_Method($this, 'needTranslatedPages'),
            'thumb'               => new \Twig_Function_Method($this, 'thumb'),
            'getPagesMenu'        => new \Twig_Function_Method($this, 'getPagesMenu', array('is_safe' => array('html'))),
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
     * Check if page model allow children
     *
     * @param Page $page
     */
    public function allowChildrens(Page $page)
    {
        $models = $this->container->getParameter('fulgurio_light_cms.models');
        return ($models[$page->getModel()]['allow_childrens']);
    }

    /**
     * Get parent page to copy page for translation
     * @param Page $page
     * @return multitype:number
     */
    public function needTranslatedPages(Page $page)
    {
       // Route page
       if (!$page->getParent() || !is_null($page->getSourceId()))
       {
            return FALSE;
       }
       if ($this->container->hasParameter('fulgurio_light_cms.languages'))
       {
            $availableLangs = $this->container->getParameter('fulgurio_light_cms.languages');
            $nbAvailableLangs = count($availableLangs);
            $availableTranslatedPages = $this->doctrine->getRepository('FulgurioLightCMSBundle:Page')->findBy(array('source_id' => $page->getId()), array('lang' => 'ASC'));
            $nbAvailableTranslatedPages = count($availableTranslatedPages);
            $availableTranslatedParents = $this->doctrine->getRepository('FulgurioLightCMSBundle:Page')->findBy(array('source_id' => $page->getParent()->getId()), array('lang' => 'ASC'));
            $nbAvailableTranslatedParents = count($availableTranslatedParents);
            // If we create a new page
            if ($page->getParent()->getMetaValue('is_home') == '1' && $nbAvailableLangs != $availableTranslatedPages)
            {
                $langs = array_flip($availableLangs);
                foreach ($availableTranslatedPages as $availableTranslatedPage)
                {
                    unset($langs[$availableTranslatedPage->getLang()]);
                }
                foreach ($langs as $lang => $value)
                {
                    if ($lang == $page->getLang())
                    {
                        unset($langs[$lang]);
                    }
                    else
                    {
                        $langs[$lang] = $page->getParent()->getId();
                    }
                }
                return ($langs);
            }
            // Normal children page
            else if ($nbAvailableTranslatedPages != $nbAvailableTranslatedParents && $nbAvailableTranslatedParents < $nbAvailableLangs)
            {
                $langs = array_flip($availableLangs);
                foreach ($availableTranslatedPages as $availableTranslatedPage)
                {
                    unset($langs[$availableTranslatedPage->getLang()]);
                }
                foreach ($availableTranslatedParents as $availableTranslatedParent)
                {
                    if (isset($langs[$availableTranslatedParent->getLang()]))
                    {
                        $langs[$availableTranslatedParent->getLang()] = $availableTranslatedParent->getId();
                    }
                }
                foreach ($langs as $lang => $value)
                {
                    if (is_null($value) || $lang == $page->getLang())
                    {
                        unset($langs[$lang]);
                    }
                }
                return ($langs);
            }
        }
        return FALSE;
    }

    /**
     * Get thumb of a picture
     *
     * @param Page $page
     */
    public function thumb($media, $size = 'small')
    {
        $thumbSizes = $this->container->getParameter('fulgurio_light_cms.thumbs');
        return LightCMSUtils::getThumbFilename($media->getFullPath(), $media->getMediaType(), $thumbSizes[$size]);
    }

    /**
     * Get page menu to display
     *
     * @param string $menuName
     * @param string $lang
     * @return array
     */
    public function getPagesMenu($menuName, $lang = NULL)
    {
//         $currentUser = $this->securityContext->getToken()->getUser();
//         if ($currentUser != 'anon.')
//         {
//             $roles = $currentUser->getRoles();
//         }
//         else
//         {
//             $roles = array();
//         }
        $pages = $this->doctrine->getRepository('FulgurioLightCMSBundle:PageMenu')->findPagesOfMenu($menuName, $lang);
        $availablePages = array();
        foreach ($pages as &$page)
        {
            if ($page->getStatus() != 'published')
            {
                continue;
            }
//             $pageAccesses = $page->getAccess();
//             if (empty($pageAccesses))
//             {
//                 $page->setAllowedChildren($this->doctrine->getRepository('FulgurioLightCMSBundle:Page')->getAllowedChildren($roles, $page));
                $availablePages[] = $page;
//             }
//             else if (is_object($currentUser))
//             {
//                 foreach ($pageAccesses as $pageAccess)
//                 {
//                     if ($currentUser->hasRole($pageAccess))
//                     {
//                         $page->setAllowedChildren($this->doctrine->getRepository('FulgurioLightCMSBundle:Page')->getAllowedChildren($roles, $page));
//                         $availablePages[] = $page;
//                         break;
//                     }
//                 }
//             }
        }
        return ($availablePages);
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