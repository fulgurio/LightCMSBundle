<?php
/*
 * This file is part of the LightCMSBundle package.
 *
 * (c) Fulgurio <http://fulgurio.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fulgurio\LightCMSBundle\Form;

use Fulgurio\LightCMSBundle\Entity\Media;
use Fulgurio\LightCMSBundle\Form\AbstractAdminHandler;
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
     * @todo : folder owner
     */
    public function process(Media $media)
    {
        if ($this->request->getMethod() == 'POST')
        {
            $this->form->bindRequest($this->request);
            if ($this->form->isValid())
            {
                $currentDate = new \DateTime();
                $file = $this->form->get('media')->getData();
                if (!is_null($file))
                {
                    if ($file->getError() == UPLOAD_ERR_OK) {
                        $oldFile = LightCMSUtils::getUploadDir(FALSE) . $media->getFullpath();
                        if (is_file($oldFile))
                        {
                            unlink($oldFile);
                        }
                        $filename = LightCMSUtils::getUniqName($file, LightCMSUtils::getUploadDir());
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
                    else {
                        return (FALSE);
                    }
                    // New media
                    if ($media->getId() == 0)
                    {
                        $media->setCreatedAt($currentDate);
                    }
                    $media->setUpdatedAt($currentDate);
                    $em = $this->doctrine->getEntityManager();
                    $em->persist($media);
                    $em->flush();
                    return (TRUE);
                }
            }
        }
        return (FALSE);
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