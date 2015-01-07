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
 * Admin user manager tests
 */
class AdminUserControllerTest extends AbstractAdminControllerTest
{
    /**
     * User list test
     */
    public function testListAction()
    {
        $client = $this->login('/admin/users/');
        $crawler = $client->getCrawler();

        // Users list
        $addUserBtn = $crawler->filter('a[href="/admin/users/add"]');
        $this->assertEquals(1, $addUserBtn->count());

        // Add form
        $crawler = $client->click($addUserBtn->link());
        $this->assertEquals(1, $crawler->filter('form[action="/admin/users/add"]')->count());
        $crawler = $client->back();

        // Edit form
        $editFirstUserBtn = $crawler->filter('form[action="/admin/users/"] table a[href$="/edit"]');
        $crawler = $client->click($editFirstUserBtn->link());
        $this->assertEquals(1, $crawler->filter('form[action="' . substr($editFirstUserBtn->link()->getUri(), strlen('http://localhost')) . '"]')->count());
        $crawler = $client->back();

        // Remove form
        $removeFirstUserBtn = $crawler->filter('form[action="/admin/users/"] table a[href$="/remove"]');
        $crawler = $client->click($removeFirstUserBtn->link());
        $this->assertEquals(1, $crawler->filter('form[action="' . substr($removeFirstUserBtn->link()->getUri(), strlen('http://localhost')) . '"]')->count());
        $crawler = $client->back();

        // 2 users in the list, each can be edited
        $this->assertEquals(2, $crawler->filter('form[action="/admin/users/"] table tbody tr a[href$="/edit"]')->count());

        // 2 users in the list, but current user can not be removed
        $this->assertEquals(1, $crawler->filter('form[action="/admin/users/"] table tbody tr a[href$="/remove"]')->count());
    }

    /**
     * Add user form test
     */
    public function testAddAction()
    {
        $client = $this->login('/admin/users/add');

        $this->addUserWithFailure($client);
        $this->addUserWithSuccess($client);
    }

    /**
     * Add wrong data user form
     *
     * @param Client $client
     */
    private function addUserWithFailure(Client $client)
    {
        $form = $client->getCrawler()->filter('button[type=submit]')->form();

        // Username, password and email are required
        $form['user[username]'] = '';
        $form['user[password][first]'] = '';
        $form['user[password][second]'] = '';
        $form['user[email]'] = '';
        $form['user[roles]'] = array();
        $crawler = $client->submit($form);
        $this->assertCount(3, $crawler->filter('div.alert-error br'));

        // Passwords are different
        $form['user[username]'] = 'test3';
        $form['user[password][first]'] = 'test3Password';
        $form['user[password][second]'] = 'wrong';
        $form['user[email]'] = 'test3@foo.bar';

        $crawler = $client->submit($form);
        $this->assertCount(1, $crawler->filter('div.alert-error br'));
    }

    /**
     * Add well data page form
     *
     * @param Client $client
     */
    private function addUserWithSuccess(Client $client)
    {
        $form = $client->getCrawler()->filter('button[type=submit]')->form();

        // set some values
        $form['user[username]'] = 'test3';
        $form['user[password][first]'] = 'test3Password';
        $form['user[password][second]'] = 'test3Password';
        $form['user[email]'] = 'test3@foo.bar';
        $form['user[roles]'] = array('ROLE_ADMIN');
        $form['user[is_active]'] = '1';

        // submit the form
        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect('/admin/users/'));
    }

    /**
     * Edit user form test
     */
    public function testEditAction()
    {
        $client = $this->login('/admin/users/');
        $crawler = $client->getCrawler();

        // Edit form
        $editFirstUserBtn = $crawler->filter('form[action="/admin/users/"] table a[href$="/edit"]');
        $crawler = $client->click($editFirstUserBtn->link());

        $form = $crawler->filter('button[type=submit]')->form();

        $this->assertNotEquals('test1Bis', $form['user[username]']->getValue());

        // set some values
        $form['user[username]'] = 'test1Bis';
        $form['user[password][first]'] = '';
        $form['user[password][second]'] = '';

        // submit the form
        $client->submit($form);
        $client->followRedirect();

        $crawler = $client->click($editFirstUserBtn->link());
        $this->assertEquals('test1Bis', $form['user[username]']->getValue());
    }

    /**
     * Remove user test
     */
    public function testRemoveAction()
    {
        $client = $this->login('/admin/users/');
        $crawler = $client->getCrawler();

        // 2 users in the list
        $this->assertEquals(2, $crawler->filter('form[action="/admin/users/"] table tbody tr')->count());

        // Remove form
        $removeFirstUserBtn = $crawler->filter('form[action="/admin/users/"] table a[href$="/remove"]');
        $crawler = $client->click($removeFirstUserBtn->link());

        // We cancel the deletion
        $form = $crawler->filter('button[type=submit][value=no]')->form();

        // submit the form
        $client->submit($form);
        $crawler = $client->followRedirect();

        // No user removed, so always 2 users
        $this->assertEquals(2, $crawler->filter('form[action="/admin/users/"] table tbody tr')->count());

        $crawler = $client->click($removeFirstUserBtn->link());

        // We valid the deletion
        $form = $crawler->filter('button[type=submit][value=yes]')->form();

        // submit the form
        $client->submit($form);
        $crawler = $client->followRedirect();

        // 1 user less
        $this->assertEquals(1, $crawler->filter('form[action="/admin/users/"] table tbody tr')->count());
    }
}