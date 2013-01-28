<?php
namespace Fulgurio\LightCMSBundle\Form;

use Fulgurio\LightCMSBundle\Entity\Page;
use Fulgurio\LightCMSBundle\Entity\PageMeta;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class AdminPageHandler
{
    /**
     * Slug suffix separator, if slug already exist
     * @var string
     */
    const SLUG_SUFFIX_SEPARATOR = '-';

    /**
     * Form object
     * @var Form
     */
    protected $form;

    /**
     * Request data object
     * @var Request
     */
    protected $request;

    /**
     * Doctrine object
     * @var RegistryInterface
     */
    protected $doctrine;

    /**
     * Meta data
     * @var array
     */
    protected $pageMetas;


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
            if ($this->request->get('realSubmit') !== '1')
            {
                return FALSE;
            }
            $this->form->bindRequest($this->request);
            if ($this->form->isValid())
            {
                $data = $this->request->get('page');
                $this->updatePageMetas($page, $data);
                $em = $this->doctrine->getEntityManager();
                // New page
                if ($page->getId() == 0)
                {
                    $page->setCreatedAt(new \DateTime());
                    if ($page->getPosition() == NULL)
                    {
                        $page->setPosition($page->getParent() ? $this->doctrine->getRepository('FulgurioLightCMSBundle:Page')->getNextPosition($page->getParent()->getId()) : 1);
                    }
                }
                $page->setSlug($this->makeSlug($page->getTitle()));
                $this->makeFullpath($page);
                $page->setUpdatedAt(new \DateTime());
                $this->beforePersist($page);
                $em->persist($page);
                $em->flush();
                return (TRUE);
            }
        }
        return (FALSE);
    }

    /**
     * Add suffix number if page exists
     *
     * @param string $slug
     * @param Page $page
     * @param integer $number
     * @return string
     */
    protected function addSuffixNumber($slug, Page $page, $number = 0)
    {
        $em = $this->doctrine->getEntityManager();
        $slugTmp = $number > 0 ? $slug . self::SLUG_SUFFIX_SEPARATOR . $number : $slug;
        $parentFullpath = $page->getParent()->getFullpath();
        $foundedPage = $this->doctrine->getRepository('FulgurioLightCMSBundle:Page')->findOneBy(array(
                'fullpath' => ($parentFullpath != '' ? ($parentFullpath . '/') : '') . $slugTmp
        ));
        if ($foundedPage && $foundedPage != $page)
        {
            return $this->addSuffixNumber($slug, $page, $number + 1);
        }
        return $slugTmp;
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
            $slug = $this->addSuffixNumber($page->getSlug(), $page);
            $page->setFullpath(($parentFullpath != '' ? ($parentFullpath . '/') : '') . $slug);
            $page->setSlug($slug);
            if ($page->hasChildren())
            {
                foreach ($page->getChildren() as $children)
                {
//                         if ($children->getModel() != 'redirect')
//                     {
                    $this->makeFullpath($children);
                    $this->doctrine->getEntityManager()->persist($children);
//                     }
                }
            }
        }
    }

    /**
     * Update page metas
     *
     * @param Page $page
     * @param array $data
     */
    protected function updatePageMetas(Page $page, $data)
    {
        $em = $this->doctrine->getEntityManager();
        $em->persist($this->initMetaEntity($page, 'meta_keywords', $data['meta_keywords']));
        $em->persist($this->initMetaEntity($page, 'meta_description', $data['meta_description']));
    }

    /**
     * Add or update a PageMeta entity, and return it for save
     *
     * @param Page $page
     * @param string $metaName
     * @param string $metaValue
     * @return \Fulgurio\LightCMSBundle\Entity\PageMeta
     */
    final protected function initMetaEntity(Page $page, $metaName, $metaValue)
    {
        $this->initPageMetas($page);
        if (isset($this->pageMetas[$metaName]))
        {
            $entity = $this->pageMetas[$metaName];
        }
        else
        {
            $entity = new PageMeta();
            $entity->setPage($page);
            $entity->setMetaKey($metaName);
        }
        $entity->setMetaValue($metaValue);
        return ($entity);
    }

    /**
     * Init page meta of page
     *
     * @param Page $page
     */
    final protected function initPageMetas(Page $page)
    {
        if (!isset($this->pageMetas))
        {
            $this->pageMetas = array();
            if ($page->getId() > 0)
            {
                $metas = $this->doctrine->getRepository('FulgurioLightCMSBundle:PageMeta')->findByPage($page->getId());
                foreach ($metas as $meta)
                {
                    $this->pageMetas[$meta->getMetaKey()] = $meta;
                }
            }
        }
    }

    /**
     * Generate slug
     *
     * @param string $title
     * @return string
     */
    final public function makeSlug($title)
    {
        $slug = strtolower($title);
        $slug = strtr(utf8_decode($slug), utf8_decode('àáâãäåòóôõöøèéêëçìíîïùúûüÿñ'), 'aaaaaaooooooeeeeciiiiuuuuyn');
        $slug = preg_replace(array('`[^a-z0-9]`i', '`[-]+`'), '-', $slug);
        return ($slug);
    }

    /**
     * Action to do before persist data object
     *
     * @param Page $page
     */
    public function beforePersist(Page &$page)
    {
        // nothing
    }

    /**
     * $form setter
     *
     * @param Form $form
     */
    public function setForm(Form $form)
    {
        $this->form = $form;
    }

    /**
     * $request setter
     *
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * $doctrine setter
     *
     * @param RegistryInterface $doctrine
     */
    public function setDoctrine(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }
}