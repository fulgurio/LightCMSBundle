<?php
/*
 * This file is part of the LightCMSBundle package.
 *
 * (c) Fulgurio <http://fulgurio.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fulgurio\LightCMSBundle\Form\Handler;

use Fulgurio\LightCMSBundle\Entity\Media;
use Fulgurio\LightCMSBundle\Form\Handler\AbstractAdminHandler;
use Fulgurio\LightCMSBundle\Utils\LightCMSUtils;

class AdminMediaHandler extends AbstractAdminHandler
{
    /**
     * Thumb sizes
     * @var array
     */
    private $thumbSizes;


    /**
     * Processing form values
     *
     * @param Media $media
     * @return boolean
     */
    public function process(Media $media)
    {
        if ($this->request->getMethod() == 'POST')
        {
            $this->form->handleRequest($this->request);
            if ($this->form->isValid())
            {
                $currentDate = new \DateTime();
                $file = $this->form->get('media')->getData();
                if (!is_null($file))
                {
                    if ($file->getError() == UPLOAD_ERR_OK)
                    {
                        $oldFile = LightCMSUtils::getUploadDir(FALSE) . $media->getFullpath();
                        if (is_file($oldFile))
                        {
                            unlink($oldFile);
                        }
                        $filename = $this->getUniqFilename(LightCMSUtils::getUploadDir(), LightCMSUtils::makeSlug($file->getClientOriginalName(), TRUE));
                        $media->setFullPath(LightCMSUtils::getUploadUrl() . $filename);
                        $media->setOriginalName($file->getClientOriginalName());
                        $mimeType = $file->getMimeType();
                        $media->setMediaType($mimeType);
                        $file->move(LightCMSUtils::getUploadDir(), $filename);
                        $mediaType = preg_split('/\//', $mimeType);
                        if (isset($this->thumbSizes) && $mediaType[0] == 'image')
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
                    }
                    else
                        {
                        return FALSE;
                    }
                    // New media
                    if ($media->getId() == 0)
                    {
                        $media->setOwner($this->user);
                        $media->setCreatedAt($currentDate);
                    }
                    else
                    {
                        $media->setUpdatedAt($currentDate);
                    }
                    $em = $this->doctrine->getEntityManager();
                    $em->persist($media);
                    $em->flush();
                    return TRUE;
                }
            }
        }
        return FALSE;
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
        $pos = mb_strrpos($filename, '.');
        $file = mb_substr($filename, 0, $pos);
        $extension = mb_substr($filename, $pos);
        $postfix = $counter > 0 ? $this->slugSuffixSeparator . $counter : '';
        if (file_exists($path . '/' . $file . $postfix . $extension))
        {
            return $this->getUniqFilename($path, $filename, $counter + 1);
        }
        return $file . $postfix . $extension;
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
}