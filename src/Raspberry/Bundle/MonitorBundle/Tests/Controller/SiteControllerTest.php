<?php

namespace Raspberry\Bundle\MonitorBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Raspberry\Bundle\MonitorBundle\Tests\TestHelpers\ClientHelper;

class SiteControllerTest extends WebTestCase
{

    public function testCompleteScenario()
    {

        $clientHelper = new ClientHelper();
        $client       = $clientHelper->getAdminClient();

        $crawler =  $client->request('GET', '/admin/site/');

        $this->assertEquals(200,  $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /admin/site/");
        $crawler = $client->click($crawler->selectLink('Create a new site')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form(array(
            'raspberry_bundle_monitorbundle_site[name]'  => 'Test',
            'raspberry_bundle_monitorbundle_site[url]'  => 'http://www.google.com',
            // ... other fields to fill
        ));

        $crawler= $client->submit($form);


        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Test")')->count(), 'Missing element td:contains("Test")');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'raspberry_bundle_monitorbundle_site[name]'  => 'Foo',
            // ... other fields to fill
        ));

        $crawler = $client->submit($form);


        // Check the element contains an attribute with value equals "Foo"
        $this->assertGreaterThan(0, $crawler->filter('[value="Foo"]')->count(), 'Missing element [value="Foo"]');

        // Delete the entity
        $crawler=$client->submit($crawler->selectButton('Delete')->form());


        // Check the entity has been delete on the list
        $this->assertEquals('http://localhost/admin/site/',  $client->getHistory()->current()->getUri(), "Not redirectect to /admin/site/ after delete");
    }

}
