<?php


namespace Raspberry\Bundle\MonitorBundle\Tests\TestHelpers;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Code from https://symfonybricks.com/en/brick/functional-tests-with-authenticated-users
 * Makes $client login as Admin
 *
 * Class ClientHelper
 * @package Raspberry\Bundle\MonitorBundle\Tests\TestHelpers
 */
class ClientHelper extends WebTestCase
{

    private $client;
    private $user;

    public function __construct()
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/');
        $this->assertTrue(200 === $client->getResponse()->getStatusCode(), "expected status code of 200, got " . $client->getResponse()->getStatusCode() . $client->getResponse()->getContent());

        $this->client = $client;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getAdminClient()
    {
        $client  = $this->getClient();
        $crawler = $client->request('GET', '/login');
        $this->assertTrue(200 === $client->getResponse()->getStatusCode(), "expected status code of 200, got " . $client->getResponse()->getStatusCode() . $client->getResponse()->getContent());

        // Fill in the form and submit it
        $form = $crawler->selectButton('_submit')->form(array(
                '_username' => 'admin',
                '_password' => 'raspadmin'
            ));

        $client->submit($form);
        $this->assertTrue(200 === $client->getResponse()->getStatusCode(), "expected status code of 200, got " . $client->getResponse()->getStatusCode() . $client->getResponse()->getContent());
        $this->client = $client;

        return $this->getClient();
    }

}
