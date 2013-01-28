<?php
namespace Fulgurio\LightCMSBundle\DataFixtures\ORM;

use Fulgurio\LightCMSBundle\Entity\Page;
use Fulgurio\LightCMSBundle\Entity\PageMeta;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadPageData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * Load fixtures
     */
    public function load(ObjectManager $manager)
    {
        $currentDate = new \DateTime();
        $homepage = new Page();
        $homepage->setPageType('page');
        $homepage->setModel('standard');
        $homepage->setCreatedAt($currentDate);
        $homepage->setUpdatedAt($currentDate);
        $homepage->setTitle('Home');
        $homepage->setContent('This is a sample text for initialisation of Light CMS');
        $homepage->setSlug('');
        $homepage->setFullpath('');
        $homepage->setPosition(1);
        $homepage->setStatus('published');
        $meta1 = $this->addMeta($homepage, 'meta_keywords', 'lightcms,fulgurio,content management system');
        $manager->persist($meta1);
        $meta2 = $this->addMeta($homepage, 'meta_description', 'Light content management system, using Symfony 2 framework');
        $manager->persist($meta2);
        $homepage->addPageMeta($meta1);
        $homepage->addPageMeta($meta2);
        $manager->persist($homepage);
        $this->addReference('homepage', $homepage);
        $manager->flush();
    }

    /**
     * Add meta to page
     *
     * @param \Fulgurio\LightCMSBundle\Entity\Page $page
     * @param string $key
     * @param string $value
     * @return \Fulgurio\LightCMSBundle\Entity\PageMeta
     */
    private function addMeta($page, $key, $value)
    {
        $meta = new PageMeta();
        $meta->setMetaKey($key);
        $meta->setMetaValue($value);
        $meta->setPage($page);
        return $meta;
    }

    /**
     * Order of fixture loading
     */
    public function getOrder()
    {
        return 1;
    }
}