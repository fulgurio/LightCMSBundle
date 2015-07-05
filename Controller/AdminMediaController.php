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
use Fulgurio\LightCMSBundle\Form\Handler\AdminMediaHandler;
use Fulgurio\LightCMSBundle\Form\Type\AdminMediaType;
use Fulgurio\LightCMSBundle\Utils\LightCMSUtils;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
        $request = $this->getRequest();
        if ($request->get('filter'))
        {
            $filters['media_type'] = $request->get('filter') . '%';
        }
        if ($request->get('filename'))
        {
            $filters['original_name'] = $request->get('filename') . '%';
        }
        $page = $request->get('page') > 1 ? $request->get('page') - 1 : 0;
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
     * @param number $mediaId if specified, we are on edit media form
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
        $request = $this->getRequest();
        $thumbSizes = $this->container->getParameter('fulgurio_light_cms.thumbs');
        $form = $this->createForm(new AdminMediaType($this->container), $media);
        $formHandler = new AdminMediaHandler();
        $formHandler->setForm($form)
                ->setRequest($request)
                ->setDoctrine($this->getDoctrine())
                ->setUser($this->getUser())
                ->setMediaLibraryService($this->get('fulgurio_light_cms.media_library'));
        if ($formHandler->process($media))
        {
            if ($request->isXmlHttpRequest())
            {
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
            $this->get('session')->getFlashBag()->add(
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
     * @param number $mediaId
     */
    public function removeAction($mediaId)
    {
        $media = $this->getMedia($mediaId);
        $request = $this->getRequest();
        if ($request->request->get('confirm') === 'yes' || $request->get('confirm') === 'yes')
        {
            $this->get('fulgurio_light_cms.media_library')->remove($media);
            $em = $this->getDoctrine()->getManager();
            $em->remove($media);
            $em->flush();
            return $this->redirect($this->generateUrl('AdminMedias'));
        }
        else if ($request->request->get('confirm') === 'no')
        {
            return $this->redirect($this->generateUrl('AdminMedias', array('mediaId' => $mediaId)));
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
        $wysiwygName = $this->container->getParameter('fulgurio_light_cms.wysiwyg');
        if ($wysiwygName && $this->container->hasParameter($wysiwygName))
        {
            return $this->container->getParameter($wysiwygName);
        }
        else
        {
            return NULL;
        }
    }

    /**
     * Get page from given ID, and ckeck if it exists
     *
     * @param number $mediaId
     * @throws NotFoundHttpException
     */
    private function getMedia($mediaId)
    {
        $page = $this->getDoctrine()
                ->getRepository('FulgurioLightCMSBundle:Media')
                ->findOneBy(array('id' => $mediaId));
        if ($page)
        {
            return $page;
        }
        throw new NotFoundHttpException(
            $this->get('translator')->trans('fulgurio.lightcms.medias.not_found', array(), 'admin')
        );
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
        return $response;
    }
}