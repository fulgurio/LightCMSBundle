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

use Fulgurio\LightCMSBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

/**
 * Fixtures of users
 *
 * @author Vincent GUERARD <v.guerard@fulgurio.net>
 */
class LoadUsers extends AbstractFixture implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load homepage
     */
    public function load(ObjectManager $manager)
    {
        $user1 = $this->createUser(
                'test1',
                'test1Password',
                'test1@test.test',
                array('ROLE_ADMIN'),
                TRUE
        );
        $user2 = $this->createUser(
                'test2',
                'test2Password',
                'test2@test.test',
                array('ROLE_ADMIN'),
                FALSE
        );
        $this->addReference('user1', $user1);
        $manager->persist($user1);
        $this->addReference('user2', $user2);
        $manager->persist($user2);
        $manager->flush();
    }

    /**
     * Create user
     *
     * @param string $login
     * @param string $password
     * @param string $email
     * @param array $roles
     * @param boolean $isActive
     * @return User
     */
    private function createUser($login, $password, $email, array $roles, $isActive)
    {
        $user = new User();
        $factory = $this->container->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $encodedPassword = $encoder->encodePassword(
                $password,
                $user->getSalt());
        $user->setUsername($login);
        $user->setPassword($encodedPassword);
        $user->setEmail($email);
        $user->setIsActive($isActive);
        $user->setRoles($roles);
        $user->setCreatedAt(new \DateTime());
        return $user;
    }

    /**
     * Order of fixture loading
     */
    public function getOrder()
    {
        return 1;
    }
}