<?php

namespace Fulgurio\LightCMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fulgurio\LightCMSBundle\Entity\PageMenu
 */
class PageMenu
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $label
     */
    private $label;

    /**
     * @var integer $position
     */
    private $position;

    /**
     * @var Fulgurio\LightCMSBundle\Entity\Page
     */
    private $page;


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
     * Set label
     *
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
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
     * Set page
     *
     * @param Fulgurio\LightCMSBundle\Entity\Page $page
     */
    public function setPage(\Fulgurio\LightCMSBundle\Entity\Page $page)
    {
        $this->page = $page;
    }

    /**
     * Get page
     *
     * @return Fulgurio\LightCMSBundle\Entity\Page 
     */
    public function getPage()
    {
        return $this->page;
    }
}