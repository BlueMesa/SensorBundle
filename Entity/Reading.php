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

use Bluemesa\Bundle\CoreBundle\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Reading
 *
 * @ORM\Entity(repositoryClass="Bluemesa\Bundle\SensorBundle\Repository\ReadingRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Reading extends Entity
{
    /**
     * @var \DateTime $timestamp
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @Serializer\Expose
     */
    private $timestamp;

    /**
     * @var Sensor
     *
     * @ORM\ManyToOne(targetEntity="Sensor", inversedBy="readings")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotBlank(message = "Sensor must be specified")
     */
    protected $sensor;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     * @Serializer\Expose
     */
    private $temperature;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     * @Serializer\Expose
     */
    private $humidity;


    /**
     * Reading constructor.
     *
     * @param float $temperature
     * @param float $humidity
     */
    public function __construct($temperature = null, $humidity = null)
    {
        $this->temperature = $temperature;
        $this->humidity = $humidity;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param \DateTime $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return Sensor
     */
    public function getSensor()
    {
        return $this->sensor;
    }

    /**
     * @param Sensor $sensor
     */
    public function setSensor(Sensor $sensor = null)
    {
        $this->sensor = $sensor;
    }

    /**
     * @return float
     */
    public function getTemperature()
    {
        return $this->temperature;
    }

    /**
     * @param float $temperature
     */
    public function setTemperature($temperature)
    {
        $this->temperature = $temperature;
    }

    /**
     * @return float
     */
    public function getHumidity()
    {
        return $this->humidity;
    }

    /**
     * @param float $humidity
     */
    public function setHumidity($humidity)
    {
        $this->humidity = $humidity;
    }
    
}
