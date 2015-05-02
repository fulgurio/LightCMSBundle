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
 * Front page controller tests
 *
 * @author Vincent GUERARD <v.guerard@fulgurio.net>
 */
class FrontPageControllerTest extends LiipWebTestCase
{
    /**
     * Init database
     */
    protected function setUp()
    {
        $this->loadFixtures(array(
            'Fulgurio\LightCMSBundle\Tests\DataFixtures\ORM\LoadHomePage'
            ));
    }

    /**
     * Test homepage
     */
    public function testShowAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertCount(
                1,
                $crawler->filter('html:contains("This is a sample text for initialisation of Light CMS")')
        );
    }
}