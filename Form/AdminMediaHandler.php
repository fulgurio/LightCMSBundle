<?php
namespace Fulgurio\LightCMSBundle\Form;

use Fulgurio\LightCMSBundle\Entity\Media;
use Fulgurio\LightCMSBundle\Form\AbstractAdminHandler;

class AdminMediaHandler extends AbstractAdminHandler
{
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
                $file = $this->form->get('media')->getData();
                if (!is_null($file))
                {
                    $oldFile = substr($this->getUploadDir(), 0, -mb_strlen($this->getUploadUrl())) . $media->getFullpath();
                    if (is_file($oldFile))
                    {
                        unlink($oldFile);
                    }
                    $filename = sha1(uniqid(mt_rand(), TRUE)) . '.' . $file->guessExtension();
//                     $file->move($this->getUploadDir(), $filename);
                    $media->setFullPath($this->getUploadUrl() . $filename);
                    $media->setOriginalName($file->getClientOriginalName());
                    $mimeType = $file->getMimeType();
                    $mediaType = preg_split('/\//', $mimeType);
                    $media->setMediaType($mediaType[0]);
//                     $file->getClientSize();
                }
                // New media
                if ($media->getId() == 0)
                {
                    $media->setCreatedAt(new \DateTime());
                }
                $media->setUpdatedAt(new \DateTime());
                $em = $this->doctrine->getEntityManager();
                $em->persist($media);
                $em->flush();
                return (TRUE);
            }
        }
        return (FALSE);
    }
}