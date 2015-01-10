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
 * Admin access tests
 *
 * @author Vincent GUERARD <v.guerard@fulgurio.net>
 */
class AdminDashboardControllerTest extends LiipWebTestCase
{
    /**
     * Init database
     */
    public function setup()
    {
        $this->loadFixtures(array('Fulgurio\LightCMSBundle\Tests\DataFixtures\ORM\LoadUsers'));
    }

    /**
     * Unauthentified access test
     */
    public function testUnauthentifiedIndexAction()
    {
        $client = static::createClient();

        $client->request('GET', '/admin/');

        $response = $client->getResponse();
        if ($client->getContainer()->has('security.firewall.map.context.lightcms_secured_area'))
        {
            $this->assertEquals($response->getStatusCode(), 302);
            $this->assertTrue($response->isRedirect('http://localhost/login'));
        }
        elseif ($client->getContainer()->has('security.firewall.map.context.secured_area'))
        {
            $this->assertEquals($response->getStatusCode(), 401);
        }

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

        if ($client->getContainer()->has('security.firewall.map.context.lightcms_secured_area'))
        {
            $this->loadFixtures(array('Fulgurio\LightCMSBundle\Tests\DataFixtures\ORM\LoadUsers'));

            $this->assertTrue($client->getResponse()->isRedirect('http://localhost/login'));

            $crawler = $client->followRedirect();

            $form = $crawler->filter('button[type=submit]')->form();

            // set some values
            $form['_username'] = 'test1';
            $form['_password'] = 'test1Password';

            // submit the form
            $client->submit($form);
            $crawler = $client->followRedirect();
        }
        elseif (!$client->getContainer()->has('security.firewall.map.context.secured_area'))
        {
            $this->markTestSkipped('No firewall is set');
        }

        $this->assertCount(
                1,
                $crawler->filter('html:contains("LightCMS")')
        );

        // User is authentified
        $security = $client->getContainer()->get('security.context');

        $this->assertTrue($security->isGranted('ROLE_ADMIN'));
    }

    /**
     * Authentification with a disabled account access test
     */
    public function testAuthentifiedWithDisabledAccountIndexAction()
    {
        $client = static::createClient();
        if ($client->getContainer()->has('security.firewall.map.context.secured_area'))
        {
            $this->markTestSkipped('No firewall is set');
        }

        $client->request('GET', '/admin/');
        $crawler = $client->followRedirect();

        $form = $crawler->filter('button[type=submit]')->form();

        // set some values
        $form['_username'] = 'test2';
        $form['_password'] = 'test2Password';

        // submit the form
        $crawler = $client->submit($form);

        // User is authentified
        $security = $client->getContainer()->get('security.context');
        $this->assertTrue($security->getToken() === NULL);
    }
}