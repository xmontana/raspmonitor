<?php
/**
 * Created by PhpStorm.
 * User: 39728369d
 * Date: 11/08/14
 * Time: 13:58
 */

namespace Raspberry\Bundle\MonitorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class emailtestCommand extends ContainerAwareCommand
{
    protected function configure()
    {

        $this
            ->setName('raspberry:monitor:email')
            ->setDescription('Test email sending.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        try {
        $message = new \Swift_Message();
        $message->setSubject('[Raspmonitor] Alert TEST')
            ->setFrom($this->getContainer()->getParameter('alert_from_email'))
            ->setTo($this->getContainer()->getParameter('alert_to_email'))
            ->setBody('test email')
            ;

        $mailer = $this->getContainer()->get('mailer');

        $mailer->send($message);

        $spool = $mailer->getTransport()->getSpool();
        $transport = $this->getContainer()->get('swiftmailer.transport.real');

        $spool->flushQueue($transport);

        $output->writeln('Enviado!');
        } catch (\Exception $e) {
            $output->writeln('error envio de email!');

        }

    }

}
