<?php

/*
 * This file is part of the BluemesaSensorBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Bluemesa\Bundle\SensorBundle\Entity;


use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class ReadingDataset
 * @package Bluemesa\Bundle\SensorBundle\Entity
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 *
 * @Serializer\ExclusionPolicy("all")
 */
class ReadingDataset
{
    /**
     * @var Sensor
     */
    private $sensor;

    /**
     * @var Collection
     */
    private $readings;

    /**
     * @var array
     * @Serializer\Expose
     */
    private $temperatures;

    /**
     * @var array
     * @Serializer\Expose
     */
    private $humidities;

    /**
     * @var array
     * @Serializer\Expose
     */
    private $timestamps;


    /**
     * ReadingDataset constructor.
     * @param Sensor     $sensor
     * @param \DateTime  $start
     * @param \DateTime  $end
     * @param int        $count
     * @param boolean    $interpolate
     */
    public function __construct(Sensor $sensor,
                                \DateTime $start,
                                \DateTime $end,
                                $count = null,
                                $interpolate = false)
    {
        $this->sensor = $sensor;
        $this->setReadings($start, $end, $count, $interpolate);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReadings()
    {
        return $this->readings;
    }

    /**
     * @param \DateTime  $start
     * @param \DateTime  $end
     * @param int|null   $count
     * @param boolean    $interpolate
     */
    public function setReadings(\DateTime $start, \DateTime $end, $count = null, $interpolate = false)
    {
        $this->temperatures = array();
        $this->humidities = array();
        $this->timestamps = array();
        $this->readings = $this->retrieveReadings($start, $end);

        if ($count === null) {
            $count = $this->readings->count() > 1152 ? 1152 : $this->readings->count();
            $count = $count <= 288 ? 288 : $count;
        }
        $i = $this->getInterval($start, $end, $count);
        $value = array();

        /** @var Reading $reading */
        foreach ($this->readings as $reading) {
            $timestamp = $this->normalizeTimestamp($reading->getTimestamp()->getTimestamp(), $i);
            $value[$timestamp][] = $reading;
        }

        $it = new \ArrayIterator($value);
        $it->rewind();
        $k0 = $it->key();
        $t0 = $this->normalizeTimestamp($start->getTimestamp(), $i);
        $tN = $this->normalizeTimestamp($end->getTimestamp(), $i);
        $kP = null;
        $vP = null;

        for ($t = $t0; $t <= $tN; $t += $i) {
            if (! $it->valid()) {
                $this->temperatures[] = null;
                $this->humidities[] = null;
                $this->timestamps[] = $t;
                continue;
            }
            $k = $it->key();
            if ($k == $k0) {
                $kP = $k;
                $vP = $it->current();
                $result = $this->averageReadings($vP);
            } else {
                $v1 = $this->averageReadings($vP);
                $v = $it->current();
                $v2 = $this->averageReadings($v);
                $result = $this->interpolateReadings($kP, $v1, $k, $v2, $t);
            }
            if ($interpolate || ($t == $k)) {
                $this->temperatures[] = $result['temperature'];
                $this->humidities[] = $result['humidity'];
            } else {
                $this->temperatures[] = null;
                $this->humidities[] = null;
            }
            $this->timestamps[] = $t;

            if ($t >= $k) {
                $it->next();
            }
        }
    }

    /**
     * @return array
     */
    public function getTemperatures()
    {
        return $this->temperatures;
    }

    /**
     * @return array
     */
    public function getHumidities()
    {
        return $this->humidities;
    }

    /**
     * @return array
     */
    public function getTimestamps()
    {
        return $this->timestamps;
    }

    public function getHumiditySeries()
    {
        $result = array();
        foreach ($this->timestamps as $key => $timepoint) {
            $humidity = $this->humidities[$key];
            if (null !== $humidity) {
                $result[] = array($timepoint * 1000, $humidity);
            }
        }

        return $result;
    }

    public function getTemperatureSeries()
    {
        $result = array();
        foreach ($this->timestamps as $key => $timepoint) {
            $temperature = $this->temperatures[$key];
            if (null !== $temperature) {
                $result[] = array($timepoint * 1000, $temperature);
            }
        }

        return $result;
    }

    /**
     * @param \DateTime $start
     * @param \DateTime $end
     * @return \Doctrine\Common\Collections\Collection
     */
    private function retrieveReadings(\DateTime $start, \DateTime $end)
    {
        return $this->sensor->getReadings($start, $end);
    }

    /**
     * @param  \DateTime $start
     * @param  \DateTime $end
     * @param  int       $count
     * @return int
     */
    private function getInterval(\DateTime $start, \DateTime $end, $count)
    {
        return intval(round(($end->getTimestamp() - $start->getTimestamp()) / $count));
    }

    /**
     * @param  int $timestamp
     * @param  int $interval
     * @return int
     */
    private function normalizeTimestamp($timestamp, $interval)
    {
        return intval(round($timestamp / $interval) * $interval);
    }

    /**
     * @param  array $readings
     * @return array
     */
    private function averageReadings(array $readings)
    {
        $temperature = 0;
        $humidity = 0;

        /** @var Reading $reading */
        foreach ($readings as $reading) {
            $temperature += $reading->getTemperature();
            $humidity += $reading->getHumidity();
        }

        return array('temperature' => $temperature / count($readings),
            'humidity' => $humidity / count($readings));
    }

    /**
     * @param  int   $t1
     * @param  array $v1
     * @param  int   $t2
     * @param  array $v2
     * @param  int   $t
     * @return array
     */
    private function interpolateReadings($t1, array $v1, $t2, array $v2, $t)
    {
        $temp1 = $v1['temperature'];
        $hum1 = $v1['humidity'];
        $temp2 = $v2['temperature'];
        $hum2 = $v2['humidity'];

        $temperature = $this->interpolate($t1, $temp1, $t2, $temp2, $t);
        $humidity = $this->interpolate($t1, $hum1, $t2, $hum2, $t);

        return array('temperature' => $temperature, 'humidity' => $humidity);
    }

    /**
     * @param  int $x1
     * @param  int $y1
     * @param  int $x2
     * @param  int $y2
     * @param  int $x
     * @return int
     */
    private function interpolate($x1, $y1, $x2, $y2, $x)
    {
        return $y2 - ((($x2 - $x) * ($y2 - $y1)) / ($x2 - $x1));
    }
}
