<?php

namespace Raspberry\Bundle\MonitorBundle\Twig;

class ServerExtension extends \Twig_Extension
{

    private $container;
    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('status', array($this, 'statusFilter'), array('is_safe' => array('all'))),
        );
    }

    public function statusFilter($errors)
    {

        $threshold= $this->container->getParameter('monitor.threshold');

        $color="";
        if ($errors == 0) {
           $color="text-success";
        } elseif ($errors < $threshold) {
           $color="text-warning";
        } elseif ($errors >= $threshold) {
           $color="text-danger";
        }

        return "<i class=\"fa fa-circle $color \"></i>";
    }

    public function getName()
    {
        return 'server_extension';
    }
}
