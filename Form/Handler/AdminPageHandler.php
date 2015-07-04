<?php
/*
 * This file is part of the LightCMSBundle package.
 *
 * (c) Fulgurio <http://fulgurio.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fulgurio\LightCMSBundle\Form\Handler;

use Fulgurio\LightCMSBundle\Entity\Page;
use Fulgurio\LightCMSBundle\Entity\PageMeta;
use Fulgurio\LightCMSBundle\Entity\PageMenu;
use Fulgurio\LightCMSBundle\Form\Handler\AbstractAdminHandler;
use Fulgurio\LightCMSBundle\Utils\LightCMSUtils;

class AdminPageHandler extends AbstractAdminHandler
{
    /**
     * Meta data
     * @var array
     */
    protected $pageMetas;


    /**
     * Processing form values
     *
     * @param Page $page
     * @return boolean
     */
    public function process(Page $page)
    {
        if ($this->request->getMethod() === 'POST')
        {
            if ($this->request->get('realSubmit') !== '1')
            {
                return FALSE;
            }
            $this->form->handleRequest($this->request);
            if ($this->form->isValid())
            {
                $data = $this->request->get('page');
                $this->updatePageMetas($page, $data);
                $this->updatePageMenuPosition($page);
                $em = $this->doctrine->getManager();
                // New page
                if ($page->getId() == 0)
                {
                    if (is_a($this->user, 'Symfony\Component\Security\Core\User\UserInterface')
                            && method_exists($this->user, 'getId'))
                    {
                        $page->setOwnerId($this->user->getId());
                    }
                    $page->setCreatedAt(new \DateTime());
                    $page->setPosition($page->getParent() ? $this->doctrine->getRepository('FulgurioLightCMSBundle:Page')->getNextPosition($page->getParent()->getId()) : 1);
                }
                else
                {
                    $page->setUpdatedAt(new \DateTime());
                }
                // Well, slug has been already made, but it s not easy to get the value, so we did it again
                $page->setSlug(LightCMSUtils::makeSlug(isset($data['lang']) ? $data['lang'] : $page->getTitle()));
                if (isset($data['lang']))
                {
                    $page->setLang($data['lang']);
                }
                else if ($page->getParent())
                {
                    $page->setLang($page->getParent()->getLang());
                }
                $this->makeFullpath($page);
                $this->beforePersist($page);
                $em->persist($page);
                $em->flush();
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Add suffix number if page exists
     *
     * @param string $slug
     * @param Page $page
     * @param number $number
     * @return string
     */
    protected function addSuffixNumber($slug, Page $page, $number = 0)
    {
        $slugTmp = $number > 0 ? $slug . $this->slugSuffixSeparator . $number : $slug;
        $parentFullpath = $page->getParent()->getFullpath();
        $foundedPage = $this->doctrine->getRepository('FulgurioLightCMSBundle:Page')->findOneBy(array(
                'fullpath' => ($parentFullpath !== '' ? ($parentFullpath . '/') : '') . $slugTmp
        ));
        if ($foundedPage && $foundedPage != $page)
        {
            return $this->addSuffixNumber($slug, $page, $number + 1);
        }
        return $slugTmp;
    }

    /**
     * Init page full path and check if it doesn't already exist
     *
     * @param Page $page
     */
    public function makeFullpath(Page $page)
    {
        if ($page->getParent() === NULL)
        {
            $page->setFullpath('');
            $page->setSlug('');
        }
        else
        {
            $parentFullpath = $page->getParent()->getFullpath();
            $slug = $this->addSuffixNumber($page->getSlug(), $page);
            $page->setFullpath(($parentFullpath != '' ? ($parentFullpath . '/') : '') . $slug);
            $page->setSlug($slug);
            if ($page->hasChildren())
            {
                foreach ($page->getChildren() as $children)
                {
                    $this->makeFullpath($children);
                    $this->doctrine->getManager()->persist($children);
                }
            }
        }
    }

    /**
     * Update page metas
     *
     * @param Page $page
     * @param array $data
     */
    protected function updatePageMetas(Page $page, array $data)
    {
        $em = $this->doctrine->getManager();
        if (isset($data['meta_keywords']))
        {
            $em->persist($this->initMetaEntity($page, 'meta_keywords', trim($data['meta_keywords'])));
        }
        if (isset($data['meta_description']))
        {
            $em->persist($this->initMetaEntity($page, 'meta_description', trim($data['meta_description'])));
        }
    }

    /**
     * Update page menu position
     *
     * @param Page $page
     */
    private function updatePageMenuPosition(Page $page)
    {
        $pageMenuRepository = $this->doctrine->getRepository('FulgurioLightCMSBundle:PageMenu');
        $em = $this->doctrine->getManager();
        $data = $this->request->get('page');
        if (isset($data['availableMenu']))
        {
            if (!is_null($page->getId()))
            {
                $menus = $page->getMenu();
                $alreadyInMenu = array();
                foreach ($menus as $menu)
                {
                    if (!in_array($menu->getLabel(), $data['availableMenu']))
                    {
                        $pageMenuRepository->upMenuPagesPosition($menu->getLabel(), $menu->getPosition() + 1);
                        $em->remove($menu);
                    }
                    else
                    {
                        $alreadyInMenu[] = $menu->getLabel();
                    }
                }
            }
            foreach ($data['availableMenu'] as $menuLabel)
            {
                // If it s a new page, or if it a page which we had a new menu
                if (!isset($alreadyInMenu) || !in_array($menuLabel, $alreadyInMenu))
                {
                    $menu = new PageMenu();
                    $menu->setPosition($pageMenuRepository->getNextMenuPosition($menuLabel));
                    $menu->setLabel($menuLabel);
                    $menu->setPage($page);
                    $em->persist($menu);
                }
            }
        }
        else if (!is_null($page->getId()))
        {
            $menus = $page->getMenu();
            foreach ($menus as $menu)
            {
                $pageMenuRepository->upMenuPagesPosition($menu->getLabel(), $menu->getPosition() + 1);
                $em->remove($menu);
            }
        }
    }

    /**
     * Add or update a PageMeta entity, and return it for save
     *
     * @param Page $page
     * @param string $metaName
     * @param string $metaValue
     * @return \Fulgurio\LightCMSBundle\Entity\PageMeta
     */
    final public function initMetaEntity(Page $page, $metaName, $metaValue)
    {
        $this->initPageMetas($page);
        if (isset($this->pageMetas[$metaName]))
        {
            $entity = $this->pageMetas[$metaName];
        }
        else
        {
            $entity = new PageMeta();
            $entity->setPage($page);
            $entity->setMetaKey($metaName);
        }
        $entity->setMetaValue($metaValue);
        return $entity;
    }

    /**
     * Init page meta of page
     *
     * @param Page $page
     */
    final protected function initPageMetas(Page $page)
    {
        if (!isset($this->pageMetas))
        {
            $this->pageMetas = array();
            if ($page->getId() > 0)
            {
                $metas = $this->doctrine->getRepository('FulgurioLightCMSBundle:PageMeta')->findByPage($page->getId());
                foreach ($metas as $meta)
                {
                    $this->pageMetas[$meta->getMetaKey()] = $meta;
                }
            }
        }
    }

    /**
     * Action to do before persist data object
     *
     * @param Page $page
     */
    public function beforePersist(Page &$page)
    {
        // nothing
    }
}