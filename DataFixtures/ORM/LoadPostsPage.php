<?php
/*
 * This file is part of the LightCMSBundle package.
*
* (c) Fulgurio <http://fulgurio.net>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Fulgurio\LightCMSBundle\DataFixtures\ORM;

use Fulgurio\LightCMSBundle\Entity\Page;
use Fulgurio\LightCMSBundle\Entity\PageMeta;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

/**
 * Fixtures of posts list
 *
 * @author Vincent GUERARD <v.guerard@fulgurio.net>
 */
class LoadPostsData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * Load posts list page
     */
    public function load(ObjectManager $manager)
    {
        $currentDate = new \DateTime();
        $postsList = new Page();
        $postsList->setPageType('page');
        $postsList->setModel('postsList');
        $postsList->setCreatedAt($currentDate);
        $postsList->setUpdatedAt($currentDate);
        $postsList->setTitle('Posts list');
        $postsList->setContent('');
        $postsList->setSlug('posts');
        $postsList->setFullpath('posts');
        $postsList->setPosition(1);
        $postsList->setStatus('draft');
        $postsList->setParent($this->getReference('homepage'));
        $manager->persist($postsList);
        $this->addReference('posts_list', $postsList);
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