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

use Fulgurio\LightCMSBundle\Tests\Controller\AbstractAdminControllerTest;
use Symfony\Bundle\FrameworkBundle\Client;

/**
 * Admin page tests
 */
class AdminPageControllerTest extends AbstractAdminControllerTest
{
    /**
     *  Page list test
     */
    public function testListAction()
    {
        $client = $this->login('/admin/pages/');
        $crawler = $client->getCrawler();
        $this->assertEquals(3, $crawler->filter('div.treeview li a')->count());
        $this->assertEquals(2, $crawler->filter('div.treeview li ul li a')->count());
        $this->assertEquals(2, $crawler->filter('div.treeview li ul li.page_standard')->count());
        $this->assertEquals(1, $crawler->filter('div.treeview li.draft')->count());
    }

    /**
     * Add page test
     */
    public function testAddAction()
    {
        $client = $this->login('/admin/pages/');
        $crawler = $client->getCrawler();

        $sample1PageLink = $crawler->filter('div.treeview li ul li a')->first();
        $crawler = $client->click($sample1PageLink->link());

        $this->assertEquals('Sample1', $crawler->filter('article h2')->html());
        $addPageLink = $crawler->filter('div section a[href$="/add"]');
        $crawler = $client->click($addPageLink->link());

        $this->assertEquals(
                $client->getContainer()->get('translator')->trans('fulgurio.lightcms.pages.add_form.legend', array(), 'admin'),
                $crawler->filter('form fieldset legend')->html()
        );

        $this->addPageWithFailure($client);
        $this->addPageWithSuccess($client);
    }

    /**
     * Add wrong data page form
     *
     * @param Client $client
     */
    private function addPageWithFailure(Client $client)
    {
        $form = $client->getCrawler()->filter('button[name=realSubmit]')->form();

        // set some values
        $form['page[model]'] = 'standard';
        $form['page[title]'] = '';
        $form['page[content]'] = '';
        $form['page[status]'] = 'published';
        $form['page[meta_keywords]'] = '';
        $form['page[meta_description]'] = '';

        // Title is empty
        $crawler = $client->submit($form);
        $this->assertCount(1, $crawler->filter('div.alert-error br'));
    }

    /**
     * Add well data page form
     *
     * @param Client $client
     */
    private function addPageWithSuccess(Client $client)
    {
        $form = $client->getCrawler()->filter('button[name=realSubmit]')->form();

        // set some values
        $form['page[model]'] = 'standard';
        $form['page[title]'] = 'Son of sample 1';
        $form['page[content]'] = 'content of son of sample 1';
        $form['page[status]'] = 'draft';
        $form['page[meta_keywords]'] = 'sample10';
        $form['page[meta_description]'] = 'page descript';

        // submit the form
        $crawler = $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());

        $crawler = $client->followRedirect();

        $this->assertEquals('Son of sample 1', $crawler->filter('article h2')->html());
        $this->assertEquals('<i class="icon-search"></i>', $crawler->filter('div section a[href="/sample1/son-of-sample-1"]')->html());
        $this->assertEquals(3, $crawler->filter('div.treeview li ul li a')->count());
        $this->assertEquals(1, $crawler->filter('div.treeview li ul li ul li a')->count());
    }

    /**
     * Edit page test
     */
    public function testEditAction()
    {
        $client = $this->login('/admin/pages/');
        $crawler = $client->getCrawler();

        $samplePageLink = $crawler->filter('div.treeview li ul li a:contains("Sample2")')->first();
        $crawler = $client->click($samplePageLink->link());

        $this->assertEquals('Sample2', $crawler->filter('article h2')->html());
        $editPageLink = $crawler->filter('div section a[href$="/edit"]');
        $crawler = $client->click($editPageLink->link());

        $this->assertEquals(
                $client->getContainer()->get('translator')->trans('fulgurio.lightcms.pages.edit_form.legend', array(), 'admin'),
                $crawler->filter('form fieldset legend')->html()
        );

        $form = $client->getCrawler()->filter('button[name=realSubmit]')->form();

        // set some values
        $form['page[title]'] = 'Sample2 test';
        $form['page[status]'] = 'published';

        // submit the form
        $crawler = $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());

        $crawler = $client->followRedirect();

        $this->assertEquals('Sample2 test', $crawler->filter('article h2')->html());
        $this->assertEquals(0, $crawler->filter('div.treeview li.draft')->count());
    }

    /**
     * Remove page test
     */
    public function testRemoveAction()
    {
        $client = $this->login('/admin/pages/');
        $crawler = $client->getCrawler();

        $homePageLink = $crawler->filter('div.treeview li > a:contains("Home")')->first();
        // Removing page with child is prohibided
        $client->request('GET', $homePageLink->attr('href') . '/remove');

        if ($client->getContainer()->has('security.context') && $client->getContainer()->get('security.context')->getToken())
        {
            $this->assertEquals(403, $client->getResponse()->getStatusCode());
        }

        $sample2PageLink = $crawler->filter('div.treeview li ul li a:contains("Sample2")')->first();
        $crawler = $client->click($sample2PageLink->link());

        $removePageLink = $crawler->filter('div section a[href$="/remove"]');

        // Confirmation is "no"
        $crawler = $client->click($removePageLink->link());
        $form = $client->getCrawler()->filter('button[value=no]')->form();
        $crawler = $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect($sample2PageLink->attr('href')));
        $crawler = $client->followRedirect();
        $this->assertEquals(3, $crawler->filter('div.treeview li a')->count());

        // Confirmation is "yes"
        $crawler = $client->click($removePageLink->link());
        $form = $client->getCrawler()->filter('button[value=yes]')->form();
        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertEquals(2, $crawler->filter('div.treeview li a')->count());
    }

    /**
     * Change page position test
     */
    public function testChangePositionAction()
    {
        $client = $this->login('/admin/pages/');
        $crawler = $client->getCrawler();

        $this->assertEquals('Sample1', $crawler->filter('div.treeview li ul li a:first-child')->html());

        $homePageLink = $crawler->filter('div.treeview li > a:contains("Home")')->first();
        $sample2PageLink = $crawler->filter('div.treeview li ul li a:contains("Sample2")')->first();
        $data = array(
            'pageId'   => substr($sample2PageLink->attr('href'), strlen('/admin/pages/')),
            'parentId' => substr($homePageLink->attr('href'), strlen('/admin/pages/')),
            'position' => '1'
        );
        $client->request(
                'POST',
                '/admin/pages/position/',
                $data,
                array(),
                array('HTTP_X-Requested-With' => 'XMLHttpRequest')
        );
        $this->assertEquals('{"code":42}', $client->getResponse()->getContent());

        // Sample2 is on first position
        $crawler = $client->request('GET', '/admin/pages/');
        $this->assertEquals('Sample2', $crawler->filter('div.treeview li ul li a:first-child')->html());
    }
}