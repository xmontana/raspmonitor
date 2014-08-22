<?php

namespace Raspberry\Bundle\MonitorBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Raspberry\Bundle\MonitorBundle\Tests\TestHelpers\ClientHelper;

class ApiControllerTest extends WebTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;
    }

    public function testCompleteScenario()
    {

        $clientHelper = new ClientHelper();
        $client       = $clientHelper->getAdminClient();

        $crawler =  $client->request('GET', '/api/servers/status');

        $this->assertEquals(200,  $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /api/servers/status");

        $crawler =  $client->request('GET', '/api/pi');
        $this->assertEquals(200,  $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /api/pi");

        $site = $this->em
            ->getRepository('RaspberryMonitorBundle:Site')
            ->findOneBy(array('name'=>'Google.com'));
        ;
        $id=$site->getId();
        $this->assertGreaterThan(0, $id);

        $crawler =  $client->request('GET', '/api/'.$id.'/time');
        $this->assertEquals(200,  $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /api/'.$id.'/time");
        $crawler =  $client->request('GET', '/api/'.$id.'/show');
        $this->assertEquals(200,  $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /api/'.$id.'/show");





    }


    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

}
