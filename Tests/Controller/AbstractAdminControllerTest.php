<?php
/*
 * This file is part of the LightCMSBundle package.
 *
 * (c) Fulgurio <http://fulgurio.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fulgurio\LightCMSBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase as LiipWebTestCase;

/**
 * Abstract used for tests
 */
abstract class AbstractAdminControllerTest extends LiipWebTestCase
{
    /**
     * Init database
     */
    public function setup()
    {
        $this->loadFixtures(array(
            'Fulgurio\LightCMSBundle\Tests\DataFixtures\ORM\LoadHomePage',
            'Fulgurio\LightCMSBundle\Tests\DataFixtures\ORM\LoadStandardPages'
    ));
    }

    /**
     * Login to a page
     *
     * @param string $url
     * @return Client
     */
    protected function login($url)
    {
        $client = static::createClient();

        if ($client->getContainer()->has('security.firewall.map.context.secured_area'))
        {
            $client->setServerParameters( array(
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW'   => 'adminpass'));
            $client->request('GET', $url);

            // User is authentified
            $security = $client->getContainer()->get('security.context');
            $this->assertTrue($security->isGranted('ROLE_ADMIN'));
        }
        else
        {
            $client->request('GET', $url);
        }
        return $client;
    }
}
