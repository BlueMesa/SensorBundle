<?php

/*
 * This file is part of the BluemesaSensorBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\SensorBundle\Controller;

use Bluemesa\Bundle\CoreBundle\Controller\RestController;
use Bluemesa\Bundle\SensorBundle\Charts\SensorChart;
use Bluemesa\Bundle\SensorBundle\Entity\Reading;
use Bluemesa\Bundle\SensorBundle\Entity\Sensor;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\View\View;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SensorController
 *
 * @package Bluemesa\Bundle\SensorBundle\Controller
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class SensorController extends RestController
{

    /**
     * @REST\View()
     * @REST\Post("/{sensor}/reading.{_format}", defaults={"_format" = "json"}, requirements={"sensor" = "\d+"})
     * @ParamConverter("sensor", class="BluemesaSensorBundle:Sensor", options={"id" = "sensor"})
     * @ParamConverter("reading", converter="fos_rest.request_body")
     *
     * @param  Request $request
     * @param  Sensor  $sensor
     * @param  Reading $reading
     * @return View
     */
    public function putReadingAction(Request $request, Sensor $sensor, Reading $reading)
    {
        $sensor->addReading($reading);
        $om = $this->getObjectManager($sensor);
        $om->persist($sensor);
        $om->flush();

        return $this->routeRedirectView('bluemesa_sensor_sensor_get',
            array('sensor' => $sensor->getId(),
                '_format' => $request->get('_format')
            )
        );
    }

    /**
     * @REST\View()
     * @REST\Get("/{sensor}.{_format}",
     *     defaults={"_format" = "json", "start" = "%date_now%", "end" = "%date_24_hours_ago%"},
     *     requirements={"sensor" = "\d+"})
     * @REST\Get("/{sensor}/from/{start}/until/{end}.{_format}",
     *     defaults={"_format" = "json", "start" = "%date_now%", "end" = "%date_24_hours_ago%"},
     *     requirements={"sensor" = "\d+"})
     * @REST\Get("/{sensor}/from/{start}.{_format}",
     *     defaults={"_format" = "json", "start" = "%date_now%", "end" = "%date_24_hours_ago%"},
     *     requirements={"sensor" = "\d+"})
     *
     * @ParamConverter("sensor", class="BluemesaSensorBundle:Sensor", options={"id" = "sensor"})
     * @ParamConverter("start")
     * @ParamConverter("end")
     *
     * @param  Request    $request
     * @param  Sensor     $sensor
     * @param  \DateTime  $start
     * @param  \DateTime  $end
     * @return View
     */
    public function getAction(Request $request, Sensor $sensor, \DateTime $start, \DateTime $end)
    {
        return $this->view(array('sensor' => $sensor, 'start' => $start, 'end' => $end));
    }

    /**
     * @ParamConverter("sensor", class="BluemesaSensorBundle:Sensor", options={"id" = "sensor"})
     * @ParamConverter("start")
     * @ParamConverter("end")
     *
     * @param  Request $request
     * @param  Sensor     $sensor
     * @param  \DateTime  $start
     * @param  \DateTime  $end
     * @return Response
     */
    public function chartAction(Request $request, Sensor $sensor, \DateTime $start, \DateTime $end)
    {
        $chart = new SensorChart($sensor, $start, $end);

        return $this->render('BluemesaSensorBundle:Sensor:chart.html.twig', array(
            'chart' => $chart, 'start' => $start, 'end' => $end
        ));
    }
}
