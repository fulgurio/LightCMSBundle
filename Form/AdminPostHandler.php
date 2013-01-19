<?php
namespace Fulgurio\LightCMSBundle\Form;

use Fulgurio\LightCMSBundle\Entity\Page;
use Fulgurio\LightCMSBundle\Form\AdminPageHandler;

class AdminPostHandler extends AdminPageHandler
{
    /**
     * Post config
     * @var array
     */
    private $config;


    /**
     * Processing form values
     *
     * @param Page $page
     * @return boolean
     */
    public function process(Page $page)
    {
        if ($this->request->getMethod() == 'POST')
        {
            $this->form->bindRequest($this->request);
            if ($this->form->isValid())
            {
                $data = $this->request->get('post');
                $this->updatePageMetas($page, $data);
                $em = $this->doctrine->getEntityManager();
                $page->setSlug($this->makeSlug($page->getTitle()));
                $this->makeFullpath($page);
                $page->setPageType('post');
                $page->setPosition(0);
                $em->persist($page);
                $em->flush();
                return (TRUE);
            }
        }
        return (FALSE);
    }

    /**
     * Init page full path and check if it doesn't already exist
     *
     * @param Page $page
     */
    public function makeFullpath(Page $page)
    {
        if ($page->getParent() == NULL)
        {
            $page->setFullpath('');
            $page->setSlug('');
        }
        else
        {
            $parentFullpath = $page->getParent()->getFullpath();
            $format = date($this->config['format']);
            $slug = $this->addSuffixNumber($format . '/' . $page->getSlug(), $page);
            $page->setFullpath(($parentFullpath != '' ? ($parentFullpath . '/') : '') . $slug);
            $page->setSlug($slug);
        }
    }

    /**
     * $config setter
     * @param array $config
     */
    public function setPostConfig($config)
    {
        $this->config = $config;
    }
}