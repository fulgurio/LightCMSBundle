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
 * Fulgurio\LightCMSBundle\Entity\Media
 */
class Media
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $original_name
     */
    private $original_name;

    /**
     * @var string $full_path
     */
    private $full_path;

    /**
     * @var string $media_type
     */
    private $media_type;

    /**
     * @var datetime $created_at
     */
    private $created_at;

    /**
     * @var datetime $updated_at
     */
    private $updated_at;


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
     * Set original_name
     *
     * @param string $originalName
     */
    public function setOriginalName($originalName)
    {
        $this->original_name = $originalName;
    }

    /**
     * Get original_name
     *
     * @return string
     */
    public function getOriginalName()
    {
        return $this->original_name;
    }

    /**
     * Set full_path
     *
     * @param string $fullPath
     */
    public function setFullPath($fullPath)
    {
        $this->full_path = $fullPath;
    }

    /**
     * Get full_path
     *
     * @return string
     */
    public function getFullPath()
    {
        return $this->full_path;
    }

    /**
     * Set media_type
     *
     * @param string $mediaType
     */
    public function setMediaType($mediaType)
    {
        $this->media_type = $mediaType;
    }

    /**
     * Get media_type
     *
     * @return string
     */
    public function getMediaType()
    {
        return $this->media_type;
    }

    /**
     * Set created_at
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    }

    /**
     * Get created_at
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param datetime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
    }

    /**
     * Get updated_at
     *
     * @return datetime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }
}