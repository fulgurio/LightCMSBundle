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
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Fixtures of a standard pages
 *
 * @author Vincent GUERARD <v.guerard@fulgurio.net>
 */
class LoadStandardPages extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $currentDate = new \DateTime();
        $sample1Page = new Page();
        $sample1Page->setTitle('Sample1');
        $sample1Page->setPageType('page');
        $sample1Page->setModel('standard');
        $sample1Page->setFullpath('sample1');
        $sample1Page->setSlug('sample1');
        $sample1Page->setPosition(1);
        $sample1Page->setStatus('published');
        $sample1Page->setCreatedAt($currentDate);
        $sample1Page->setUpdatedAt($currentDate);
        $sample1Page->setParent($this->getReference('page-homepage'));
        $manager->persist($sample1Page);

//        $meta = new PageMeta();
//        $meta->setMetaKey('target_link');
//        $meta->setMetaValue('/');
//        $meta->setPage($sample1Page);

//        $manager->persist($meta);

        $sample2Page = new Page();
        $sample2Page->setTitle('Sample2');
        $sample2Page->setPageType('page');
        $sample2Page->setModel('standard');
        $sample2Page->setFullpath('sample2');
        $sample2Page->setSlug('sample2');
        $sample2Page->setPosition(2);
        $sample2Page->setStatus('draft');
        $sample2Page->setCreatedAt($currentDate);
        $sample2Page->setUpdatedAt($currentDate);
        $sample2Page->setParent($this->getReference('page-homepage'));
        $manager->persist($sample2Page);

        $manager->flush();
    }

    /**
     * Order of fixture loading
     */
    public function getOrder()
    {
        return 2;
    }
}