<?php
/*
 * This file is part of the LightCMSBundle package.
 *
 * (c) Fulgurio <http://fulgurio.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fulgurio\LightCMSBundle\Command;

use Fulgurio\LightCMSBundle\Entity\User;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;

class UserAddCommand extends DoctrineCommand
//class UserAddCommand
{
    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this
        ->setName('user:add')
        ->setDescription('Add user')
        ->addArgument('username', InputArgument::REQUIRED, 'Username')
        ->addArgument('password', InputArgument::REQUIRED, 'Password')
        ->addArgument('email', InputArgument::REQUIRED, 'Email')
        ->addArgument('role', InputArgument::OPTIONAL, 'Role(s)')
        ;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = new User();
        $username = $input->getArgument('username');
        $factory = $this->getContainer()->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword(
                $input->getArgument('password'),
                $user->getSalt());
        $email = $input->getArgument('email');
        $roles = $input->getArgument('role');
        if (!$roles)
        {
            $roles = 'ROLE_USER';
        }
        $user->setUsername($username);
        $user->setPassword($password);
        $user->setEmail($email);
        $user->setIsActive(TRUE);
        $user->setRoles(array($roles));
        $user->setCreatedAt(new \DateTime());
        $em = $this->getEntityManager(NULL);
        $em->persist($user);
        $em->flush();
    }
}