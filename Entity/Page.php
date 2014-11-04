<?php
/*
 * This file is part of the LightCMSBundle package.
 *
 * (c) Fulgurio <http://fulgurio.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fulgurio\LightCMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fulgurio\LightCMSBundle\Entity\Page
 */
class Page
{
    /**
     * @var string $page_type
     */
    protected $page_type = 'page';

    /**
     * @var string $model
     */
    private $model = 'standard';


    /**
     * Get meta value of a given meta key
     *
     * @param string $metaKey
     * @return string
     */
    public function getMetaValue($metaKey)
    {
        $metas = $this->getMeta();
        foreach ($metas as $meta)
        {
            if ($meta->getMetaKey() == $metaKey)
            {
                return ($meta->getMetaValue());
            }
        }
        return '';
    }

    /**
     * Chech if page has children page
     *
     * @return boolean
     */
    public function hasChildren()
    {
        return (count($this->getChildren()) > 0);
    }

    /**
     * Chech if page is the root page
     *
     * @return boolean
     */
    public function isRoot()
    {
        return ($this->getFullpath() == '');
    }

    /**
     * Get available menu
     *
     * @return NULL|multitype:string
     */
    public function getAvailableMenu()
    {
        $availableMenus = array();
        $menus = $this->getMenu();
        if (!$menus)
        {
            return (NULL);
        }
        foreach ($menus as $menu)
        {
            $availableMenus[] = $menu->getLabel();
        }
        return ($availableMenus);
    }


    public function setAvailableMenu($selectedMenus)
    {
    }

    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $parent_id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $fullpath;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var integer
     */
    private $position;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $lang;

    /**
     * @var integer
     */
    private $source_id;

    /**
     * @var \DateTime
     */
    private $created_at;

    /**
     * @var \DateTime
     */
    private $updated_at;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $children;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $meta;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $menu;

    /**
     * @var \Fulgurio\LightCMSBundle\Entity\Page
     */
    private $parent;

    /**
     * @var \Fulgurio\LightCMSBundle\Entity\User
     */
    private $owner;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->meta = new \Doctrine\Common\Collections\ArrayCollection();
        $this->menu = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set parent_id
     *
     * @param integer $parentId
     * @return Page
     */
    public function setParentId($parentId)
    {
        $this->parent_id = $parentId;

        return $this;
    }

    /**
     * Get parent_id
     *
     * @return integer
     */
    public function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * Set model
     *
     * @param string $model
     * @return Page
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set page_type
     *
     * @param string $pageType
     * @return Page
     */
    public function setPageType($pageType)
    {
        $this->page_type = $pageType;

        return $this;
    }

    /**
     * Get page_type
     *
     * @return string
     */
    public function getPageType()
    {
        return $this->page_type;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Page
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set fullpath
     *
     * @param string $fullpath
     * @return Page
     */
    public function setFullpath($fullpath)
    {
        $this->fullpath = $fullpath;

        return $this;
    }

    /**
     * Get fullpath
     *
     * @return string
     */
    public function getFullpath()
    {
        return $this->fullpath;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Page
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Page
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Page
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Page
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set lang
     *
     * @param string $lang
     * @return Page
     */
    public function setLang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Get lang
     *
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * Set source_id
     *
     * @param integer $sourceId
     * @return Page
     */
    public function setSourceId($sourceId)
    {
        $this->source_id = $sourceId;

        return $this;
    }

    /**
     * Get source_id
     *
     * @return integer
     */
    public function getSourceId()
    {
        return $this->source_id;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Page
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return Page
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set owner
     *
     * @param \Fulgurio\LightCMSBundle\Entity\User $owner
     * @return Page
     */
    public function setOwner(\Fulgurio\LightCMSBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \Fulgurio\LightCMSBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Add children
     *
     * @param \Fulgurio\LightCMSBundle\Entity\Page $children
     * @return Page
     */
    public function addChild(\Fulgurio\LightCMSBundle\Entity\Page $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Fulgurio\LightCMSBundle\Entity\Page $children
     */
    public function removeChild(\Fulgurio\LightCMSBundle\Entity\Page $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add meta
     *
     * @param \Fulgurio\LightCMSBundle\Entity\PageMeta $meta
     * @return Page
     */
    public function addMetum(\Fulgurio\LightCMSBundle\Entity\PageMeta $meta)
    {
        $this->meta[] = $meta;

        return $this;
    }

    /**
     * Remove meta
     *
     * @param \Fulgurio\LightCMSBundle\Entity\PageMeta $meta
     */
    public function removeMetum(\Fulgurio\LightCMSBundle\Entity\PageMeta $meta)
    {
        $this->meta->removeElement($meta);
    }

    /**
     * Get meta
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * Add menu
     *
     * @param \Fulgurio\LightCMSBundle\Entity\PageMenu $menu
     * @return Page
     */
    public function addMenu(\Fulgurio\LightCMSBundle\Entity\PageMenu $menu)
    {
        $this->menu[] = $menu;

        return $this;
    }

    /**
     * Remove menu
     *
     * @param \Fulgurio\LightCMSBundle\Entity\PageMenu $menu
     */
    public function removeMenu(\Fulgurio\LightCMSBundle\Entity\PageMenu $menu)
    {
        $this->menu->removeElement($menu);
    }

    /**
     * Get menu
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Set parent
     *
     * @param \Fulgurio\LightCMSBundle\Entity\Page $parent
     * @return Page
     */
    public function setParent(\Fulgurio\LightCMSBundle\Entity\Page $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Fulgurio\LightCMSBundle\Entity\Page
     */
    public function getParent()
    {
        return $this->parent;
    }
}
