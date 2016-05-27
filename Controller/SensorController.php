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

use Bluemesa\Bundle\SensorBundle\Entity\Reading;
use Bluemesa\Bundle\SensorBundle\Entity\Sensor;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class SensorController
 *
 * @package Bluemesa\Bundle\SensorBundle\Controller
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class SensorController extends FOSRestController
{

    /**
     * @REST\View()
     * @REST\Put("/{sensor}/reading.{_format}", defaults={"_format" = "json"}, requirements={"sensor" = "\d+"})
     * @ParamConverter("sensor", class="BluemesaSensorBundle:Sensor", options={"id" = "sensor"})
     * @ParamConverter("reading", converter="fos_rest.request_body")
     *
     * @param  Sensor  $sensor
     * @param  Reading $reading
     * @return View
     */
    public function putReadingAction(Sensor $sensor, Reading $reading)
    {
        $view = View::create();
        $sensor->addReading($reading);
        $om = $this->get('bluemesa.core.doctrine.registry')->getManagerForClass($sensor);
        $om->persist($sensor);
        $om->flush();

        $view->setData(array('sensor' => $sensor));

        return $view;
    }

    /**
     * @REST\View()
     * @REST\Get("/{sensor}.{_format}", defaults={"_format" = "json"}, requirements={"sensor" = "\d+"})
     * @ParamConverter("sensor", class="BluemesaSensorBundle:Sensor", options={"id" = "sensor"})
     *
     * @param  Sensor  $sensor
     * @return View
     */
    public function getAction(Sensor $sensor)
    {
        $view = View::create();
        $view->setData(array('sensor' => $sensor));

        return $view;
    }
}
