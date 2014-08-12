<?php

namespace Raspberry\Bundle\MonitorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use APY\DataGridBundle\Grid\Source\Entity;

/**
 * @Route("/")
 */
class DefaultController extends Controller
{

    /**
     * Dashboard controller
     *
     * @Route("/", name="dashboard")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        /// get all monitored sites:
        $sites = $em->getRepository('RaspberryMonitorBundle:Site')->findAll();

        return array('sites' => $sites);
    }

    /**
     * Shows server graph
     *
     * @Route("/server/{id}", requirements={"id" = "\d+"}, name="dashboard_server")
     * @Template()
     */

    public function serverAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        /// get all monitored sites:
        $site = $em->getRepository('RaspberryMonitorBundle:Site')->find($id);

        return array('site' => $site);
    }

    /**
     * @Route("/logs", name="logs")
     */

    public function logsAction()
    {
        $source = new Entity('RaspberryMonitorBundle:Log');

        $grid = $this->get('grid');

        $grid->setSource($source);

        $grid->setDefaultOrder('createdAt', 'desc');
        $grid->setRouteUrl($this->generateUrl('logs'));

        return $grid->getGridResponse('RaspberryMonitorBundle:Default:logs.html.twig');

    }

    /**
     * @Route("/alarms", name="alarms")
     * @Template()
     */

    public function alarmsAction()
    {

        $source = new Entity('RaspberryMonitorBundle:Alarm');

        $grid = $this->get('grid');

        $grid->setSource($source);

        $grid->setDefaultOrder('createdAt', 'desc');
        $grid->setRouteUrl($this->generateUrl('alarms'));

        return $grid->getGridResponse('RaspberryMonitorBundle:Default:logs.html.twig');

    }

}
