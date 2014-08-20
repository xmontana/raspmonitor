<?php


namespace Raspberry\Bundle\MonitorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

/**
 * @Route("/api")
 */

class ApiController extends Controller
{

    /**
     * Return time spend to load a site
     *
     * @Route("/{id}/time", requirements={"id" = "\d+"}, name="api_time")
     *
     * @param $id
     * @return JsonResponse
     */

    public function timeAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $site= $em->getRepository('RaspberryMonitorBundle:Site')->find($id);
        /// get all monitored sites:
        $log = $em->getRepository('RaspberryMonitorBundle:Log')->findTimes($id);

        $res= array();
        foreach ($log as $l) {
            $res[]=array($l['id'],$l['total_time']);
         };

        $response = new JsonResponse();
        $response->setData(array (
                'label' => $site->getName(),
                'data' => $res
            ));
        $response->headers->set('Content-Type', 'application/json');

        return $response;

    }

    /**
     *
     * Return the site info
     *
     * @Route("/{id}/show", requirements={"id" = "\d+"}, name="api_show")
     * @param $id
     * @return JsonResponse
     */

    public function statusAction($id)
    {

        $encoders = array( new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $em = $this->getDoctrine()->getManager();

        $site= $em->getRepository('RaspberryMonitorBundle:Site')->find($id);

        $response = new JsonResponse();
        $response->setData(array (
                'label' => $site->getName(),
                'data' => $serializer->serialize($site, 'json')
            ));
        $response->headers->set('Content-Type', 'application/json');

        return $response;

    }

    /**
     * Return all servers status
     *
     * @Route("/servers/status",  name="api_servers_status")
     * @return JsonResponse
     */

    public function serversAction()
    {

        $encoders = array( new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $em = $this->getDoctrine()->getManager();

        $site= $em->getRepository('RaspberryMonitorBundle:Site')->findall();

        $res= array();

        foreach ($site as $s) {
            if ($s->getLastCheck()) {
                $lastcheck=$s->getLastCheck()->format ('d/m H:i:s');
            } else {
                $lastcheck="-";
            }
            $res[]= array( $s->getId() => array( 'errors' => $s->getErrors(), 'lastcheck' =>  $lastcheck ));

        }

        $response = new JsonResponse($res );
        $response->headers->set('Content-Type', 'application/json');

        return $response;

    }

    /**
     *
     * Return Temp and CPU load of the RPI
     *
     * @Route("/pi",  name="api_pi")
     * @return JsonResponse
     */

    public function getPiAction()
    {
        $temperature = "";
        $cpu = "";

        if (function_exists('sys_getloadavg')) {
               try {
                $cpu = @sys_getloadavg();
                } catch (\Exception $e) {
                   return false;
               }
         }

        try {
            $t=@file_get_contents('/sys/class/thermal/thermal_zone0/temp');
            if ($t === FALSE) {
                $temperature = "";
            } else {
                $temperature = number_format(floatval($t)/1000,2);
            }
        } catch (\Exception $e) {
            return false;
        }
        $response = new JsonResponse();
        $response->setData(array (
            'temp' => $temperature,
            'cpu' => $cpu,
        ));
        $response->headers->set('Content-Type', 'application/json');

        return $response;

    }

}
