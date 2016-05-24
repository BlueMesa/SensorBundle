<?php

namespace Bluemesa\Bundle\SensorBundle\Entity;

use Bluemesa\Bundle\CoreBundle\Entity\Entity;
use Bluemesa\Bundle\CoreBundle\Entity\NamedInterface;
use Bluemesa\Bundle\CoreBundle\Entity\NamedTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Sensor
 *
 * @ORM\Entity(repositoryClass="Bluemesa\Bundle\SensorBundle\Repository\SensorRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Sensor extends Entity implements NamedInterface
{
    use NamedTrait;

    /**
     * @ORM\OneToMany(targetEntity="Reading", mappedBy="sensor", cascade={"persist", "remove"}, fetch="EXTRA_LAZY")
     *
     * @var Collection
     */
    protected $readings;

    
    /**
     * @return Collection
     */
    public function getReadings()
    {
        return $this->readings;
    }

    /**
     * Add reading
     *
     * @param Reading $reading
     */
    public function addReading(Reading $reading)
    {
        $readings = $this->getReadings();
        if (! $readings->contains($reading)) {
            $readings->add($reading);
        }

    }
    
    /**
     * Remove reading
     *
     * @param Reading $reading
     */
    public function removeReading(Reading $reading)
    {
        $this->getReadings()->removeElement($reading);
    }

    /**
     * Record new sensor reading
     *
     * @param float $temperature
     * @param float $humidity
     */
    public function recordReading($temperature, $humidity)
    {
        $this->addReading(new Reading($temperature, $humidity));
    }
}
