<?php

namespace Fulgurio\LightCMSBundle\Controller;

use Fulgurio\LightCMSBundle\Entity\Media;
use Fulgurio\LightCMSBundle\Form\AdminMediaHandler;
use Fulgurio\LightCMSBundle\Form\AdminMediaType;
use Fulgurio\LightCMSBundle\Utils\LightCMSUtils;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class AdminMediaController extends Controller
{
    /**
     * Medias list
     */
    public function listAction()
    {
        $nbPerPage = 10;
        $request = $this->container->get('request');
        $page = $this->get('request')->get('page') > 1 ? $request->get('page') - 1 : 0;
        $mediasNb = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:Media')->count();
        if ($request->isXmlHttpRequest())
        {
            $medias = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:Media')->findAllWithPagination($nbPerPage, $page * $nbPerPage);
            $thumbSizes = $this->container->getParameter('fulgurio_light_cms.thumbs');
            foreach ($medias as &$media)
            {
                $media['thumb'] = LightCMSUtils::getThumbFilename($media['full_path'], $media['media_type'], $thumbSizes['medium']);
            }
            return $this->jsonResponse((object) array(
                'medias' => $medias,
                'nbMedias' => $mediasNb
            ));
        }
        else
        {
            //@todo : pagination
            $medias = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:Media')->findBy(array(), array('created_at' => 'DESC'), $nbPerPage, $page * $nbPerPage);
            return $this->render('FulgurioLightCMSBundle:AdminMedia:list.html.twig', array(
                'medias' => $medias,
                'nbMedias' => $mediasNb
            ));
        }
    }

    /**
     * Add media
     */
    public function addAction()
    {
        $media = new Media();
        return $this->createMedia($media);
    }

    /**
     * Edit media
     *
     * @param integer $mediaId if specified, we are on edit media form
     */
    function editAction($pageId)
    {
        $media = $this->getMedia($mediaId);
        return $this->createPage($media);
    }

    /**
     * Create form for media entity, use for edit or add a media
     *
     * @param Media $media
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    private function createMedia(Media $media)
    {
        $request = $this->container->get('request');
        $form = $this->createForm(new AdminMediaType($this->container), $media);
        $formHandler = new AdminMediaHandler();
        $formHandler->setForm($form);
        $formHandler->setRequest($this->get('request'));
        $formHandler->setDoctrine($this->getDoctrine());
        $formHandler->setThumbSizes($this->container->getParameter('fulgurio_light_cms.thumbs'));
        if ($formHandler->process($media))
        {
            if ($request->isXmlHttpRequest())
            {
                $thumbSizes = $this->container->getParameter('fulgurio_light_cms.thumbs');
                return $this->jsonResponse((object) array('files' => array(
                    (object) array(
                        'id' => $media->getId(),
                        'name' => $media->getOriginalName(),
                        'url' => $media->getFullPath(),
                        'thumbnail_url' => LightCMSUtils::getThumbFilename($media->getFullPath(), $media->getMediaType(), $thumbSizes['medium']),
                        'delete_url' => $this->generateUrl('AdminMediasRemove', array('mediaId' => $media->getId(), 'confirm' => 'yes')),
                        'delete_type' => 'GET'
                    )
                )));
            }
            $this->get('session')->setFlash(
                'success',
                $this->get('translator')->trans(
                    isset($options['pageId']) ? 'fulgurio.lightcms.medias.edit_form.success_msg' : 'fulgurio.lightcms.medias.add_form.success_msg',
                    array(),
                    'admin'
                )
            );
            return $this->redirect($this->generateUrl('AdminMedias'));
        }
        $options['form'] = $form->createView();
        return $this->render('FulgurioLightCMSBundle:AdminMedia:add.html.twig', $options);
    }

    /**
     * Remove media, with confirm form
     *
     * @param integer $mediaId
     */
    public function removeAction($mediaId)
    {
        $media = $this->getMedia($mediaId);
        $request = $this->container->get('request');
        if ($request->request->get('confirm') === 'yes' || $request->get('confirm') === 'yes')
        {
            $filename = LightCMSUtils::getUploadDir(FALSE) . $media->getFullPath();
            if (is_file($filename))
            {
                unlink($filename);
            }
            foreach ($this->container->getParameter('fulgurio_light_cms.thumbs') as $size)
            {
                if (substr($media->getMediaType(), 0, 5) == 'image')
                {
	                $filename = LightCMSUtils::getUploadDir(FALSE) . LightCMSUtils::getThumbFilename($media->getFullPath(), $media->getMediaType(), $size);
	                if (is_file($filename))
	                {
	                    unlink($filename);
	                }
                }
            }
            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($media);
            $em->flush();
            return new RedirectResponse($this->generateUrl('AdminMedias'));
        }
        else if ($request->request->get('confirm') === 'no')
        {
            return new RedirectResponse($this->generateUrl('AdminMedias', array('mediaId' => $mediaId)));
        }
        $templateName = $request->isXmlHttpRequest() ? 'FulgurioLightCMSBundle::adminConfirmAjax.html.twig' : 'FulgurioLightCMSBundle::adminConfirm.html.twig';
        return $this->render($templateName, array(
            'action' => $this->generateUrl('AdminMediasRemove', array('mediaId' => $mediaId)),
            'confirmationMessage' => $this->get('translator')->trans('fulgurio.lightcms.medias.delete_confirm_message', array('%FILENAME%' => $media->getOriginalName()), 'admin'),
        ));
    }

    /**
     * Wysiwyg media popup
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function wysiwygAction()
    {
        $form = $this->createForm(new AdminMediaType($this->container), new Media());
        return $this->render(
            'FulgurioLightCMSBundle:AdminMedia:wysiwygAdd.html.twig',
            array(
                'form' => $form->createView(),
                'wysiwyg' => $this->container->get('request')->get('wyziwyg')
            )
        );
    }

    /**
     * Get page from given ID, and ckeck if it exists
     *
     * @param integer $pageId
     * @throws NotFoundHttpException
     */
    private function getMedia($mediaId)
    {
        if (!$page = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:Media')->findOneBy(array('id' => $mediaId)))
        {
            throw new NotFoundHttpException(
                $this->get('translator')->trans('fulgurio.lightcms.medias.not_found')
            );
        }
        return ($page);
    }

    /**
     * Return a JSON response
     *
     * @param void $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function jsonResponse($data)
    {
        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        return ($response);
    }
}