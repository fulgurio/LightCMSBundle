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
 * Fulgurio\LightCMSBundle\Entity\PageMeta
 */
class PageMeta
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $meta_key;

    /**
     * @var string
     */
    private $meta_value;

    /**
     * @var \Fulgurio\LightCMSBundle\Entity\Page
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
     * Set meta_key
     *
     * @param string $metaKey
     * @return PageMeta
     */
    public function setMetaKey($metaKey)
    {
        $this->meta_key = $metaKey;

        return $this;
    }

    /**
     * Get meta_key
     *
     * @return string 
     */
    public function getMetaKey()
    {
        return $this->meta_key;
    }

    /**
     * Set meta_value
     *
     * @param string $metaValue
     * @return PageMeta
     */
    public function setMetaValue($metaValue)
    {
        $this->meta_value = $metaValue;

        return $this;
    }

    /**
     * Get meta_value
     *
     * @return string 
     */
    public function getMetaValue()
    {
        return $this->meta_value;
    }

    /**
     * Set page
     *
     * @param \Fulgurio\LightCMSBundle\Entity\Page $page
     * @return PageMeta
     */
    public function setPage(\Fulgurio\LightCMSBundle\Entity\Page $page = null)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return \Fulgurio\LightCMSBundle\Entity\Page 
     */
    public function getPage()
    {
        return $this->page;
    }
}
