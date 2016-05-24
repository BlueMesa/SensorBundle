<?php

namespace Bluemesa\Bundle\SensorBundle\Entity;

use Bluemesa\Bundle\CoreBundle\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Gedmo\Mapping\Annotation as Gedmo;


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
     * @ORM\ManyToOne(targetEntity="Sensor", inversedBy="readings")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotBlank(message = "Sensor must be specified")
     * @Serializer\Expose
     */
    protected $stock;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $temperature;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $humidity;


    /**
     * Reading constructor.
     *
     * @param float $temperature
     * @param float $humidity
     */
    public function __construct($temperature, $humidity)
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
