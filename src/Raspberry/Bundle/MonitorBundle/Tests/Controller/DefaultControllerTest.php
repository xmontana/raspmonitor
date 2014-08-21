<?php

namespace Raspberry\Bundle\MonitorBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Raspberry\Bundle\MonitorBundle\Tests\TestHelpers\ClientHelper;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $clientHelper = new ClientHelper();
        $client       = $clientHelper->getAdminClient();
        $crawler      = $client->request('GET', '/');
        $this->assertTrue(200 === $client->getResponse()->getStatusCode(), "expected status code of 200, got " . $client->getResponse()->getStatusCode() . $client->getResponse()->getContent());
        $this->assertTrue($crawler->filter('html:contains("RaspMonitor")')->count() > 0, "The text 'RaspMonitor' was not found" . $client->getResponse()->getContent());


    }
}
