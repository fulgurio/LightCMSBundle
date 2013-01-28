<?php
namespace Fulgurio\LightCMSBundle\DataFixtures\ORM;

use Fulgurio\LightCMSBundle\Entity\Page;
use Fulgurio\LightCMSBundle\Entity\PageMeta;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadPostsData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * Load fixtures
     */
    public function load(ObjectManager $manager)
    {
        $currentDate = new \DateTime();
        $postsList = new Page();
        $postsList->setPageType('page');
        $homepage->setCreatedAt($currentDate);
        $homepage->setUpdatedAt($currentDate);
        $postsList->setTitle('Posts list');
        $postsList->setContent('');
        $postsList->setSlug('posts');
        $postsList->setFullpath('posts');
        $postsList->setPosition(1);
        $postsList->setStatus('published');
//         $postsList->setParent($this->getReference('homepage'));
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