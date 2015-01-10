<?php
/*
 * This file is part of the LightCMSBundle package.
 *
 * (c) Fulgurio <http://fulgurio.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fulgurio\LightCMSBundle\Tests\DataFixtures\ORM;

use Fulgurio\LightCMSBundle\Entity\Page;
use Fulgurio\LightCMSBundle\Entity\PageMeta;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

/**
 * Fixtures of homepage
 *
 * @author Vincent GUERARD <v.guerard@fulgurio.net>
 */
class LoadHomePage extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * Load homepage
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
        $meta3 = $this->addMeta($homepage, 'is_home', TRUE);
        $manager->persist($meta3);
        $homepage->addMetum($meta1);
        $homepage->addMetum($meta2);
        $homepage->addMetum($meta3);

        $homepage->setOwner($this->getReference('user1'));

        $manager->persist($homepage);
        $manager->flush();

        $this->addReference('page-homepage', $homepage);
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
        return 2;
    }
}