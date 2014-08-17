<?php


namespace Raspberry\Bundle\MonitorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PurgeCommand extends ContainerAwareCommand
{
    protected function configure()
    {

        $this
            ->setName('raspberry:monitor:purge')
            ->setDescription('Purge log records older than 1 week.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $q=  $this->getContainer()->get('doctrine')->getManager()->createQueryBuilder();

        $q->delete('RaspberryMonitorBundle:Log','u')
            ->where($q->expr()->lt('u.createdAt', ':createdat'))
            ->setParameter('createdat',  new \Datetime('-1 day'));

        $q->getQuery()->execute();

    }

}
