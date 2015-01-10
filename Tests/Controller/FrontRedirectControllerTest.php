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
 * Redirect front page controller tests
 *
 * @author Vincent GUERARD <v.guerard@fulgurio.net>
 */
class FrontRedirectControllerTest extends LiipWebTestCase
{
    /**
     * Redirection page test
     */
    public function testRedirectAction()
    {
        $this->loadFixtures(array(
            'Fulgurio\LightCMSBundle\Tests\DataFixtures\ORM\LoadHomePage',
            'Fulgurio\LightCMSBundle\Tests\DataFixtures\ORM\LoadRedirectPage',
            'Fulgurio\LightCMSBundle\Tests\DataFixtures\ORM\LoadUsers'
        ));
        $client = static::createClient();

        $client->request('GET', '/redirection-to-homepage');
        $this->assertTrue($client->getResponse()->isRedirect('/'));
    }
}