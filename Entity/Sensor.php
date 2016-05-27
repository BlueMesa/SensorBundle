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
     * @Serializer\Expose
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
            $reading->setSensor($this);
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
