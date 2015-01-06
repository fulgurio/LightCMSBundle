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
 * Fixtures of a redirect page
 *
 * @author Vincent GUERARD <v.guerard@fulgurio.net>
 */
class LoadRedirectPage extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $currentDate = new \DateTime();
        $redirectPage = new Page();
        $redirectPage->setTitle('Redirection to homepage');
        $redirectPage->setPageType('page');
        $redirectPage->setModel('redirect');
        $redirectPage->setFullpath('redirection-to-homepage');
        $redirectPage->setSlug('redirection-to-homepage');
        $redirectPage->setPosition(1);
        $redirectPage->setStatus('published');
        $redirectPage->setCreatedAt($currentDate);
        $redirectPage->setUpdatedAt($currentDate);

        $redirectPage->setParent($this->getReference('page-homepage'));

        $meta = new PageMeta();
        $meta->setMetaKey('target_link');
        $meta->setMetaValue('/');
        $meta->setPage($redirectPage);

        $manager->persist($redirectPage);
        $manager->persist($meta);
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