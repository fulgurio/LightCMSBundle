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

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Admin access tests
 *
 * @author Vincent GUERARD <v.guerard@fulgurio.net>
 */
class AdminDashboardControllerTest extends WebTestCase
{
    /**
     * Unauthentified access test
     */
    public function testUnauthentifiedIndexAction()
    {
        $client = static::createClient();

        $client->request('GET', '/admin/');
        $response = $client->getResponse();
        $this->assertEquals($response->getStatusCode(), 401);

        // User is not authentified
        $security = $client->getContainer()->get('security.context');
        $this->assertFalse($security->isGranted('ROLE_USER'));
    }

    /**
     * Authentification access test
     */
    public function testAuthentifiedIndexAction()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'adminpass'
        ));
        $crawler = $client->request('GET', '/admin/');

        $this->assertCount(
                1,
                $crawler->filter('html:contains("LightCMS")')
        );

        // User is authentified
        $security = $client->getContainer()->get('security.context');
        $this->assertTrue($security->isGranted('ROLE_USER'));
    }
}