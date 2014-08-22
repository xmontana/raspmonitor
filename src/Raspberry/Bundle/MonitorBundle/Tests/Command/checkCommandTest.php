<?php

namespace Raspberry\Bundle\MonitorBundle\Tests\Command;

use Raspberry\Bundle\MonitorBundle\Command\CheckCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application as App;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Raspberry\Bundle\MonitorBundle\Entity\Site;

class checkCommandTest   extends WebTestCase
{

    private $app;
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->app= new App(static::$kernel);
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;
    }


    public function testExecute()
    {
        $application = $this->app;
        $application->add(new CheckCommand());

        $command = $application->find('raspberry:monitor:check');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $this->assertRegExp('//', $commandTester->getDisplay());

        /// create a false site:
        $site_error=new Site();
        $site_error->setName('Error_site');
        $site_error->setUrl('http://www.asdfer2341123.kui');

        $this->em->persist($site_error);
        $this->em->flush();

        $commandTester_dns = new CommandTester($command);
        $commandTester_dns->execute(array('command' => $command->getName()));
        $this->assertRegExp('/Error_site/', $commandTester_dns->getDisplay());

        /// force warning for DNS error:
        $site_error->setErrors(4);
        $this->em->persist($site_error);
        $this->em->flush();

        $commandTester_dns->execute(array('command' => $command->getName()));
        $this->assertRegExp('/Alert/', $commandTester_dns->getDisplay());

        $commandTester_dns->execute(array('command' => $command->getName()));
        $this->assertRegExp('/down/', $commandTester_dns->getDisplay());


        $site_url= new Site();
        $site_url->setName('Error_url');
        $site_url->setUrl('http://www.terra.es/fw23j4nh1.lki');
        $this->em->persist($site_url);
        $this->em->flush();

        $commandTester_url = new CommandTester($command);
        $commandTester_url->execute(array('command' => $command->getName()));
        $this->assertRegExp('/Error_url/', $commandTester_url->getDisplay());

        $site_url->setErrors(4);
        $this->em->persist($site_url);
        $this->em->flush();


        $commandTester_url->execute(array('command' => $command->getName()));
        $this->assertRegExp('/Alert/', $commandTester_url->getDisplay());

        $site_error->setErrors(100);

        $commandTester_url->execute(array('command' => $command->getName()));
        $this->assertRegExp('/down/', $commandTester_url->getDisplay());




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