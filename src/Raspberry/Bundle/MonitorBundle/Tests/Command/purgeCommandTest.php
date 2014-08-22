<?php

namespace Raspberry\Bundle\MonitorBundle\Tests\Command;

use Raspberry\Bundle\MonitorBundle\Command\PurgeCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application as App;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class purgeCommandTest   extends WebTestCase
{
    private $app;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->app= new App(static::$kernel);

    }


    public function testExecute()
    {
        $application = $this->app;
        $application->add(new PurgeCommand());

        $command = $application->find('raspberry:monitor:purge');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $this->assertRegExp('//', $commandTester->getDisplay());


    }



}