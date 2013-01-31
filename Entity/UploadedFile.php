<?php

namespace Fulgurio\LightCMSBundle\Entity;

use Fulgurio\LightCMSBundle\Utils\LightCMSUtils;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Fulgurio\LightCMSBundle\Entity\Page
 */
class UploadedFile
{
    /**
     * Assert\File(maxSize="6000000")
     */
    private $attachment;

    /**
     * Set attachment
     *
     * @param string $file
     */
    public function setAttachment($file)
    {
        $this->attachment = $file;
    }

    /**
     * Get attachment
     *
     * @return string
     */
    public function getAttachment()
    {
        return $this->attachment;
    }

    /**
     * Move the uploaded file
     */
    public function upload()
    {
        $filename = LightCMSUtils::getUniqName($this->attachment, LightCMSUtils::getUploadDir());
        $this->attachment->move(LightCMSUtils::getUploadDir(), $filename);
        return (LightCMSUtils::getUploadUrl() . $filename);
    }
}