<?php

namespace Fulgurio\LightCMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fulgurio\LightCMSBundle\Entity\Page
 */
class Page
{
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
     * @var integer $id
     */
    private $id;

    /**
     * @var string $title
     */
    private $title;

    /**
     * @var string $fullpath
     */
    private $fullpath;

    /**
     * @var string $slug
     */
    private $slug;

    /**
     * @var integer $position
     */
    private $position;

    /**
     * @var string $status
     */
    private $status;

    /**
     * @var text $content
     */
    private $content;

    /**
     * @var string $model
     */
    private $model;

    /**
     * @var Fulgurio\LightCMSBundle\Entity\Page
     */
    private $children;

    /**
     * @var Fulgurio\LightCMSBundle\Entity\PageMeta
     */
    private $meta;

    /**
     * @var Fulgurio\LightCMSBundle\Entity\Page
     */
    private $parent;

    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    $this->meta = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
     */
    public function setFullpath($fullpath)
    {
        $this->fullpath = $fullpath;
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
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
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
     */
    public function setPosition($position)
    {
        $this->position = $position;
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
     */
    public function setStatus($status)
    {
        $this->status = $status;
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
     * @param text $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Get content
     *
     * @return text
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set model
     *
     * @param string $model
     */
    public function setModel($model)
    {
        $this->model = $model;
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
     * Add children
     *
     * @param Fulgurio\LightCMSBundle\Entity\Page $children
     */
    public function addPage(\Fulgurio\LightCMSBundle\Entity\Page $children)
    {
        $this->children[] = $children;
    }

    /**
     * Get children
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add meta
     *
     * @param Fulgurio\LightCMSBundle\Entity\PageMeta $meta
     */
    public function addPageMeta(\Fulgurio\LightCMSBundle\Entity\PageMeta $meta)
    {
        $this->meta[] = $meta;
    }

    /**
     * Get meta
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * Set parent
     *
     * @param Fulgurio\LightCMSBundle\Entity\Page $parent
     */
    public function setParent(\Fulgurio\LightCMSBundle\Entity\Page $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get parent
     *
     * @return Fulgurio\LightCMSBundle\Entity\Page
     */
    public function getParent()
    {
        return $this->parent;
    }
}