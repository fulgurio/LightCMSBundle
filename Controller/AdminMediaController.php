<?php
/*
 * This file is part of the LightCMSBundle package.
 *
 * (c) Fulgurio <http://fulgurio.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fulgurio\LightCMSBundle\Controller;

use Fulgurio\LightCMSBundle\Entity\Media;
use Fulgurio\LightCMSBundle\Form\AdminMediaHandler;
use Fulgurio\LightCMSBundle\Form\AdminMediaType;
use Fulgurio\LightCMSBundle\Utils\LightCMSUtils;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller managing medias
 */
class AdminMediaController extends Controller
{
    /**
     * Medias list
     */
    public function listAction()
    {
        $filters = array();
        //@todo : put the number on config
        $nbPerPage = 24;
        $request = $this->container->get('request');
        if ($request->get('filter'))
        {
            $filters['media_type'] = $request->get('filter') . '%';
        }
        if ($request->get('filename'))
        {
            $filters['original_name'] = $request->get('filename') . '%';
        }
        $page = $this->get('request')->get('page') > 1 ? $request->get('page') - 1 : 0;
        $mediasNb = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:Media')->count($filters);
        if ($request->isXmlHttpRequest())
        {
            $medias = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:Media')->findAllWithPagination($filters, $nbPerPage, $page * $nbPerPage, TRUE);
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
            $medias = $this->getDoctrine()->getRepository('FulgurioLightCMSBundle:Media')->findAllWithPagination($filters, $nbPerPage, $page * $nbPerPage);
            return $this->render('FulgurioLightCMSBundle:AdminMedia:list.html.twig', array(
                'medias' => $medias,
                'nbMedias' => $mediasNb,
                'pageCount' => ceil($mediasNb / $nbPerPage),
                'current' => $page + 1,
                'route' => 'AdminMedias',
                'query' => array(
                        'filter' => $request->get('filter') ? $request->get('filter') : '',
                        'filename' => $request->get('filename') ? $request->get('filename') : ''
                        )
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
    function editAction($mediaId)
    {
        $media = $this->getMedia($mediaId);
        return $this->createMedia($media);
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
        $formHandler->setUser($this->get('security.context')->getToken()->getUser());
        $formHandler->setSlugSuffixSeparator($this->container->getParameter('fulgurio_light_cms.slug_suffix_separator'));
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
        else if ($request->isXmlHttpRequest())
        {
            //@todo : well, we only manage one error, not the others ...
            return $this->jsonResponse((object) array('files' => array(
                    (object) array(
                            'error' => $this->get('translator')->trans('fulgurio.lightcms.medias.add_form.error_msg', array('%MAX_FILE_SIZE%' => ini_get('upload_max_filesize')), 'admin'),
                    )
            )));
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
    public function wysiwygMediaAction()
    {
        $form = $this->createForm(new AdminMediaType($this->container), new Media());
        return $this->render(
            'FulgurioLightCMSBundle:AdminMedia:wysiwygAdd.html.twig',
            array(
                'form' => $form->createView(),
                'wysiwyg' => $this->getWysiwyg()
            )
        );
    }

    /**
     * Wysiwyg link popup
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function wysiwygLinkAction()
    {
        $form = $this->createForm(new AdminMediaType($this->container), new Media());
        return $this->render(
                'FulgurioLightCMSBundle:AdminMedia:wysiwygAdd.html.twig',
                array(
                        'form' => $form->createView(),
                        'wysiwyg' => $this->getWysiwyg(),
                        'isLink' => TRUE
                )
        );
    }

    /**
     * Get specified wysiwig with configuration if set
     *
     * @return \Symfony\Component\DependencyInjection\mixed|NULL
     */
    private function getWysiwyg()
    {
        if ($this->container->getParameter('fulgurio_light_cms.wysiwyg') && $this->container->hasParameter($this->container->getParameter('fulgurio_light_cms.wysiwyg')))
        {
            return $this->container->getParameter($this->container->getParameter('fulgurio_light_cms.wysiwyg'));
        }
        else
        {
            return NULL;
        }
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
                $this->get('translator')->trans('fulgurio.lightcms.medias.not_found', array(), 'admin')
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