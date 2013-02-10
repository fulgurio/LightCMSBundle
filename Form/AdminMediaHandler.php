<?php
namespace Fulgurio\LightCMSBundle\Form;

use Fulgurio\LightCMSBundle\Entity\Media;
use Fulgurio\LightCMSBundle\Form\AbstractAdminHandler;
use Fulgurio\LightCMSBundle\Utils\LightCMSUtils;

class AdminMediaHandler extends AbstractAdminHandler
{
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
                    $oldFile = LightCMSUtils::getUploadDir(FALSE) . $media->getFullpath();
                    if (is_file($oldFile))
                    {
                        unlink($oldFile);
                    }
                    $filename = LightCMSUtils::getUniqName($file, LightCMSUtils::getUploadDir());
                    $media->setFullPath(LightCMSUtils::getUploadUrl() . $filename);
                    $media->setOriginalName($file->getClientOriginalName());
                    $mimeType = $file->getMimeType();
                    $mediaType = preg_split('/\//', $mimeType);
                    $media->setMediaType($mediaType[0]);
                    $file->move(LightCMSUtils::getUploadDir(), $filename);
                    if (isset($this->thumbSizes))
                    {
                        foreach ($this->thumbSizes as $size)
                        {
                            //@todo: choose crop or resize
                            LightCMSUtils::cropPicture(
                                LightCMSUtils::getUploadDir() . $filename,
                                LightCMSUtils::getUploadDir() . LightCMSUtils::getThumbFilename($filename, $size),
                                $size['width'],
                                $size['height']
                            );
                        }
                    }
//                  $file->getClientSize();
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
        return (FALSE);
    }

    public function setThumbSizes($sizes)
    {
        $this->thumbSizes = $sizes;
    }
}