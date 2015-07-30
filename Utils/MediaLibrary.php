<?php
/*
 * This file is part of the LightCMSBundle package.
 *
 * (c) Fulgurio <http://fulgurio.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fulgurio\LightCMSBundle\Utils;

use Fulgurio\LightCMSBundle\Entity\Media;
use Fulgurio\LightCMSBundle\Utils\LightCMSUtils;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * LightCMS utils
 */
class MediaLibrary
{
    /**
     * Thumb sizes
     * @var array
     */
    private $thumbSizes;

    /**
     * Slug suffix separator, if slug already exist
     * @var string
     */
    protected $slugSuffixSeparator = '-';


    public function add(UploadedFile $file, Media $media)
    {
//        if (!self::isImage($file))
//        {
//            return FALSE;
//        }
        if ($file->getError() == UPLOAD_ERR_OK)
        {
            $filename = $this->getUniqFilename(LightCMSUtils::getUploadDir(), LightCMSUtils::makeSlug($file->getClientOriginalName(), TRUE));
            $mimeType = $file->getMimeType();
            $file->move(LightCMSUtils::getUploadDir(), $filename);
            if (isset($this->thumbSizes))
            {
                foreach ($this->thumbSizes as $size)
                {
                    //@todo: choose crop or resize
                    LightCMSUtils::cropPicture(
                        LightCMSUtils::getUploadDir() . $filename,
                        LightCMSUtils::getUploadDir() . LightCMSUtils::getThumbFilename($filename, $mimeType, $size),
                        $size['width'],
                        $size['height']
                    );
                }
            }

            return LightCMSUtils::getUploadUrl() . $filename;
        }
        else
        {
            return FALSE;
        }
    }

    public function remove(Media $media)
    {
            $filename = LightCMSUtils::getUploadDir(FALSE) . $media->getFullPath();
            if (is_file($filename))
            {
                unlink($filename);
            }
            foreach ($this->thumbSizes as $size)
            {
                $filename = LightCMSUtils::getUploadDir(FALSE) . LightCMSUtils::getThumbFilename($media->getFullPath(), $media->getMediaType(), $size);
                if (is_file($filename))
                {
                    unlink($filename);
                }
            }
    }

    /**
     * Get uniq name for upload
     *
     * @param string $path
     * @param string $filename
     * @param number $counter
     * @return string
     */
    private function getUniqFilename($path, $filename, $counter = 0)
    {
        if (($pos = mb_strrpos($filename, '.')) == FALSE)
        {
            $file = $filename;
            $extension = '';
        }
        else
        {
            $file = mb_substr($filename, 0, $pos);
            $extension = mb_substr($filename, $pos);
        }
        $postfix = $counter > 0 ? $this->slugSuffixSeparator . $counter : '';
        if (file_exists($path . '/' . $file . $postfix . $extension))
        {
            return $this->getUniqFilename($path, $filename, $counter + 1);
        }
        return $file . $postfix . $extension;
    }

    /**
     * Check if uploaded file is an image
     *
     * @param UploadedFile $file
     * @return boolean
     */
    static public function isImage(UploadedFile $file)
    {
        return substr($file->getMimeType(), 0, 6) == 'image/';
    }

    /**
     * $thumbsizes setter
     *
     * @param array $sizes
     */
    public function setThumbSizes($sizes)
    {
        $this->thumbSizes = $sizes;
    }

    /**
     * $slugSuffixSeparator setter
     *
     * @param string $suffix
     * @return AbstractAdminHandler
     */
    public function setSlugSuffixSeparator($suffix)
    {
        if ($suffix != '')
        {
            $this->slugSuffixSeparator = $suffix;
        }

        return $this;
    }
}