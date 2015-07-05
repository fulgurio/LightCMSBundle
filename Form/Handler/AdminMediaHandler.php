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
use Fulgurio\LightCMSBundle\Utils\MediaLibrary;

class AdminMediaHandler extends AbstractAdminHandler
{
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
                $file = $this->form->get('media')->getData();
                if (!is_null($file))
                {
                    $media->setMediaType($file->getMimeType());
                    $media->setOriginalName($file->getClientOriginalName());
                    $filename = $this->mediaLibraryService->add($file, $media);
                    if ($filename)
                    {
                        // New media
                        if ($media->getId() == 0)
                        {
                            if (is_a($this->user, 'Symfony\Component\Security\Core\User\UserInterface')
                                    && method_exists($this->user, 'getId'))
                            {
                                $media->setOwnerId($this->user->getId());
                            }
                        }
                        else
                        {
                            // We remove old files
                            $this->mediaLibraryService->remove($media);
                        }

                        // We set new file
                        $media->setFullPath($filename);

                        $em = $this->doctrine->getManager();
                        $em->persist($media);
                        $em->flush();
                        return TRUE;
                    }
                }
            }
        }
        return FALSE;
    }

    /**
     * $mediaLibraryService setter
     *
     * @param Fulgurio\LightCMSBundle\Form\Handler\MediaLibrary $service
     * @return Fulgurio\LightCMSBundle\Form\Handler\AdminMediaHandler
     */
    public function setMediaLibraryService(MediaLibrary $service)
    {
        $this->mediaLibraryService = $service;

        return $this;
    }
}