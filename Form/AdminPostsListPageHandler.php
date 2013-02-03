<?php
namespace Fulgurio\LightCMSBundle\Form;

use Fulgurio\LightCMSBundle\Form\AdminPageHandler;
use Fulgurio\LightCMSBundle\Entity\Page;

class AdminPostsListPageHandler extends AdminPageHandler
{
    /**
     * Update page metas
     *
     * @param Page $page
     * @param array $data
     */
    protected function updatePageMetas(Page $page, $data)
    {
        parent::updatePageMetas($page, $data);
        $em = $this->doctrine->getEntityManager();
        $em->persist($this->initMetaEntity($page, 'nb_posts_per_page', $data['nb_posts_per_page']));
    }
}