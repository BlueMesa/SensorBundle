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
use Bluemesa\Bundle\CoreBundle\Entity\DatePeriod;
use Bluemesa\Bundle\SensorBundle\Charts\SensorChart;
use Bluemesa\Bundle\SensorBundle\Entity\Reading;
use Bluemesa\Bundle\SensorBundle\Entity\Sensor;
use Bluemesa\Bundle\SensorBundle\Form\SensorChartType;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

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
     * @REST\Post("/{sensor}/reading.{_format}", defaults={"_format" = "html"}, requirements={"sensor" = "\d+"})
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
     *     defaults={"_format" = "html", "period" = "24"}, requirements={"sensor" = "\d+"})
     * @REST\Post("/{sensor}.{_format}",
     *     defaults={"_format" = "html", "period" = "24"}, requirements={"sensor" = "\d+"})
     * @REST\Get("/{sensor}/from/{start}/until/{end}.{_format}",
     *     defaults={"_format" = "html"}, requirements={"sensor" = "\d+"})
     * @REST\Get("/{sensor}/from/{start}.{_format}",
     *     defaults={"_format" = "html"}, requirements={"sensor" = "\d+"})
     *
     * @ParamConverter("sensor", class="BluemesaSensorBundle:Sensor", options={"id" = "sensor"})
     * @ParamConverter("period")
     *
     * @param  Request     $request
     * @param  Sensor      $sensor
     * @param  DatePeriod  $period
     * @return View
     */
    public function getAction(Request $request, Sensor $sensor, DatePeriod $period)
    {
        $form = $this->createForm(SensorChartType::class, $period, array(
            'action' => $this->generateUrl('bluemesa_sensor_sensor_get', array(
                'sensor' => $sensor->getId(),
                '_format' => $request->get('_format')))
        ));
        $form->handleRequest($request);
        $chart = new SensorChart($sensor, $period);
        $view = $this->view()
            ->setData(array('sensor' => $sensor))
            ->setTemplateData(array(
                'period' => $period,
                'form' => $form->createView(),
                'chart' => $chart));

        return $view;
    }
}
