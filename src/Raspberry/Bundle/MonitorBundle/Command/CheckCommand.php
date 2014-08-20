<?php


namespace Raspberry\Bundle\MonitorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Guzzle\Http\Exception\CurlException;
use Raspberry\Bundle\MonitorBundle\Entity\Log;
use Raspberry\Bundle\MonitorBundle\Entity\Site;
use Raspberry\Bundle\MonitorBundle\Entity\Alarm;

class CheckCommand extends ContainerAwareCommand
{
    protected function configure()
    {

        $this
            ->setName('raspberry:monitor:check')
            ->setDescription('Check the status of the monitored sites.')
            ->addOption('threshold', null, InputOption::VALUE_REQUIRED, 'Number of error for notify a down site.');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $client = $this->getContainer()->get('guzzle.client');

        $em = $this->getContainer()->get('doctrine')->getManager();

        $threshold =  $input->getOption('threshold');

        if (!$threshold) {
            $threshold = $this->getContainer()->getParameter('monitor.threshold');
        }
        /// get all monitored sites:
        $sites = $em->getRepository('RaspberryMonitorBundle:Site')->findAll();

        /// $row  isused for table format output (-v)
        $rows=array();

        foreach ($sites as $site) {

            try {
                $log= new Log();
                $response = $client->get($site->getUrl())->send();

                $site->setErrors(0);
                $site->setLastCheck(new \DateTime());

                $log->setSite($site);
                $log->setMessage($response->getStatusCode());
                $log->setTotalTime($response->getInfo('total_time'));

                $em->persist($site);
                $em->persist($log);
                $rows[]=array($log->getCreatedAt()->format('d/m/Y h:i:s'), $site->getName(), $log->getTotalTime());

                unset($log);

            } catch (\Exception $e) {

                /// network error (curl)
                if ($e instanceof CurlException) {
                    $site->setErrors($site->getErrors()+1);
                    $site->setLastCheck(new \DateTime());
                    $log= new Log();
                    $log->setSite($site);
                    $log->setMessage($e->getError());
                    $log->setError($e->getErrorNo());

                    $em->persist($log);
                    $em->persist($site);

                    if ($site->getErrors() < $threshold ) {
                        // TODO: enciende LED AMARILLO
                        $rows[]=array($log->getCreatedAt()->format('d/m/Y h:i:s'), $site->getName(), '<info>Connect error</info>');
                        $output->writeln(sprintf("<info>Error ocurred: site %s failed %d times</info>: %s", $site->getName(), $site->getErrors(), $e->getError()));
                    } elseif ($site->getErrors() == $threshold ) {
                        // TODO: enciende LED ROJO


                        $rows[]=array($log->getCreatedAt()->format('d/m/Y h:i:s'), $site->getName(), '<error>ALERT</error>');
                        $output->writeln(sprintf("<error>Alert Activated: %s </error>", $site->getName()));
                        $alarma= new Alarm();
                        $alarma->setSite($site);
                        $alarma->setMessage($e->getError());
                        $alarma->setError($e->getErrorNo());
                        $em->persist($alarma);
                        unset($alarm);

                        // envia email de alerta
                        try {
                            $message = new \Swift_Message();
                                    $message->setSubject('[Raspmonitor] Alert - '.$site->getName())
                                        ->setFrom($this->getContainer()->getParameter('alert_from_email'))
                                        ->setTo($this->getContainer()->getParameter('alert_to_email'))
                                        ->setBody(
                                            $this->getContainer()->get('templating')->render(
                                                'RaspberryMonitorBundle:Email:alert.txt.twig',
                                                array('site' => $site)
                                            )
                                        );

                            $mailer = $this->getContainer()->get('mailer');
                            $mailer->send($message);
                            $spool = $mailer->getTransport()->getSpool();
                            $transport = $this->getContainer()->get('swiftmailer.transport.real');

                            $spool->flushQueue($transport);
                        } catch (\Exception $e) {
                            $output->writeln('error envio de email!');

                        }

                        //$this->activateAlert($output, $site);
                    } elseif ($site->getErrors() > $threshold ) {
                        $rows[]=array($log->getCreatedAt()->format('d/m/Y h:i:s'), $site->getName(), '<error>DOWN</error>');
                        $output->writeln(sprintf("<error>Site %s down for %d times</error>: %s", $site->getName(), $site->getErrors(), $e->getError()));
                    }

                } else {

                    $site->setErrors($site->getErrors()+1);
                    $log= new Log();
                    $log->setSite($site);
                    $log->setMessage( $e->getResponse());
                    $log->setError($e->getResponse()->getStatusCode());

                    $em->persist($log);
                    $em->persist($site);

                    if ($site->getErrors() < $threshold ) {
                        // TODO: enciende LED AMARILLO
                        $rows[]=array($log->getCreatedAt()->format('d/m/Y h:i:s'), $site->getName(), '<info>Connect error</info>');
                        $output->writeln(sprintf("<info>Error ocurred: site %s failed %d times</info>: %s", $site->getName(), $site->getErrors(), $e->getResponse()->getStatusCode()));
                    } elseif ($site->getErrors() == $threshold ) {
                        // TODO: enciende LED ROJO

                        $rows[]=array($log->getCreatedAt()->format('d/m/Y h:i:s'), $site->getName(), '<error>ALERT</error>');
                        $output->writeln(sprintf("<error>Alert Activated: %s </error>", $site->getName()));
                        // envia email de alerta
                        try {
                            $message = new \Swift_Message();
                            $message->setSubject('[Raspmonitor] Alert - '.$site->getName())
                                ->setFrom($this->getContainer()->getParameter('alert_from_email'))
                                ->setTo($this->getContainer()->getParameter('alert_to_email'))
                                ->setBody(
                                    $this->getContainer()->get('templating')->render(
                                        'RaspberryMonitorBundle:Email:alert.txt.twig',
                                        array('site' => $site)
                                    )
                                );

                            $mailer = $this->getContainer()->get('mailer');
                            $mailer->send($message);
                            $spool = $mailer->getTransport()->getSpool();
                            $transport = $this->getContainer()->get('swiftmailer.transport.real');

                            $spool->flushQueue($transport);
                        } catch (\Exception $e) {
                            $output->writeln('error envio de email!');

                        }
                    } elseif ($site->getErrors() > $threshold ) {
                        $rows[]=array($log->getCreatedAt()->format('d/m/Y h:i:s'), $site->getName(), '<error>DOWN</error>');
                        $output->writeln(sprintf("<error>Site %s down for %d times</error>: %s", $site->getName(), $site->getErrors(), $e->getResponse()->getStatusCode()));
                    }

                }

            }

        }

        if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
            $table = $this->getHelper('table');

            $table->setHeaders(array('Date', 'Site', 'Total Time'));

            $table->setRows($rows);
            $table->render($output);

        }

        $em->flush();

    }



}
