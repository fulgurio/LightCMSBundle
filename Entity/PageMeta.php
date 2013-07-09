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
     * @var integer $id
     */
    private $id;

    /**
     * @var string $meta_key
     */
    private $meta_key;

    /**
     * @var text $meta_value
     */
    private $meta_value;

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
     * Set meta_key
     *
     * @param string $metaKey
     */
    public function setMetaKey($metaKey)
    {
        $this->meta_key = $metaKey;
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
     * @param text $metaValue
     */
    public function setMetaValue($metaValue)
    {
        $this->meta_value = $metaValue;
    }

    /**
     * Get meta_value
     *
     * @return text
     */
    public function getMetaValue()
    {
        return $this->meta_value;
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