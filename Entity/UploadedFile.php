<?php

namespace Fulgurio\LightCMSBundle\Entity;

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
     * Upload directory
     * @return string
     */
    public function getUploadDir()
    {
        return 'uploads/lightCMS/';
    }

    /**
     * Get absolut upload directory
     *
     * @return string
     */
    public function getUploadRootDir()
    {
        if (is_dir(__DIR__ . '/../../../../web/'))
        {
            if (!is_dir(__DIR__ . '/../../../../web/' . $this->getUploadDir()))
            {
                mkdir(__DIR__ . '/../../../../web/' . $this->getUploadDir(), 0777, TRUE);
            }
            return __DIR__ . '/../../../../web/' . $this->getUploadDir();
        }
        else
        {
            if (!is_dir(__DIR__ . '/../../../../../../web/' . $this->getUploadDir()))
            {
                mkdir(__DIR__ . '/../../../../../../web/' . $this->getUploadDir(), 0777, TRUE);
            }
            return __DIR__ . '/../../../../../../web/' . $this->getUploadDir();
        }
    }

    /**
     * Get a randomw filename, and check if file does'nt exist
     *
     * @param UploadedFile $file
     * @param string $path
     */
    public function getUniqName($file, $path)
    {
        $filename = uniqid() . '.' . $file->guessExtension();
        if (!file_exists($path . $filename)) {
            return ($filename);
        }
        $this->getUniqName($file, $path);
    }

    /**
     * Move the uploaded file
     */
    public function upload()
    {
        $filename = $this->getUniqName($this->attachment, $this->getUploadRootDir());
        $this->attachment->move($this->getUploadRootDir(), $filename);
        return ($this->getUploadDir() . $filename);
    }
}