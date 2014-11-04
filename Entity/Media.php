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
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $original_name;

    /**
     * @var string
     */
    private $full_path;

    /**
     * @var string
     */
    private $media_type;

    /**
     * @var \DateTime
     */
    private $created_at;

    /**
     * @var \DateTime
     */
    private $updated_at;

    /**
     * @var \Fulgurio\LightCMSBundle\Entity\User
     */
    private $owner;


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
     * @return Media
     */
    public function setOriginalName($originalName)
    {
        $this->original_name = $originalName;

        return $this;
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
     * @return Media
     */
    public function setFullPath($fullPath)
    {
        $this->full_path = $fullPath;

        return $this;
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
     * @return Media
     */
    public function setMediaType($mediaType)
    {
        $this->media_type = $mediaType;

        return $this;
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
     * @param \DateTime $createdAt
     * @return Media
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
     * @return Media
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
     * @return Media
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
}
