<?php

/*
 * This file is part of the XXX.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Bluemesa\Bundle\SensorBundle\Charts;


use Bluemesa\Bundle\CoreBundle\Entity\DatePeriod;
use Bluemesa\Bundle\SensorBundle\Entity\Sensor;
use Ob\HighchartsBundle\Highcharts\Highchart;

class SensorChart extends Highchart
{
    /**
     * SensorChart constructor.
     * @param Sensor $sensor
     * @param  DatePeriod  $period
     */
    public function __construct(Sensor $sensor, DatePeriod $period)
    {
        parent::__construct();

        $dataset = $sensor->getDataset($period->getStart(), $period->getEnd());

        $this->chart->renderTo('chart');
        $this->chart->type('spline');
        $this->series(array(
            array(
                "name" => "Temperature",
                "data" => $dataset->getTemperatureSeries(),
                "color" => '#d9534f'
            ),
            array(
                "name" => "Humidity",
                "data" => $dataset->getHumiditySeries(),
                "color" => '#428bca',
                "yAxis" => 1
            )
        ));

        $this->title->text('Temperature and humidity readings');
        $this->subtitle->text(
            $sensor->getName() . " from " .
            $period->getStart()->format("d M Y H:m:s") . " until " .
            $period->getEnd()->format("d M Y H:m:s"));
        $this->xAxis->type('datetime');
        $this->yAxis(array(
            array(
                'title' => array('text'  => "Temperature [℃]", 'style' => array('color' => '#d9534f')),
                'labels' =>  array('style' => array('color' => '#d9534f')),
                'min' => 15,
                'max' => 35
            ),
            array(
                'title' => array('text'  => "Humidity [%rH]", 'style' => array('color' => '#428bca')),
                'labels' =>  array('style' => array('color' => '#428bca')),
                'min' => 0,
                'max' => 100,
                'opposite' => true
            )
        ));
    }
}
