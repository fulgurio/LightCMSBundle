<?php
namespace Fulgurio\LightCMSBundle\Form;

use Fulgurio\LightCMSBundle\Form\AdminPageHandler;
use Fulgurio\LightCMSBundle\Entity\Page;

class AdminRedirectPageHandler extends AdminPageHandler
{
    /**
     * Update page metas
     *
     * @param Page $page
     * @param array $data
     */
    protected function updatePageMetas(Page $page, $data)
    {
        $em = $this->doctrine->getEntityManager();
        $em->persist($this->initMetaEntity($page, 'target_link', $data['target_link']));
    }
}