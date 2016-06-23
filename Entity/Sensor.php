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
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;


/**
 * Sensor
 *
 * @ORM\Entity(repositoryClass="Bluemesa\Bundle\SensorBundle\Repository\SensorRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @Serializer\ExclusionPolicy("all")
 */
class Sensor extends Entity implements NamedInterface
{
    use NamedTrait;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default"=600})
     * @Serializer\Expose
     *
     * @var integer
     */
    protected $updateRate;

    /**
     * @ORM\Column(type="float", nullable=false, options={"default"=25})
     * @Serializer\Expose
     *
     * @var float
     */
    protected $presetTemperature;

    /**
     * @ORM\Column(type="float", nullable=false, options={"default"=50})
     * @Serializer\Expose
     *
     * @var float
     */
    protected $presetHumidity;

    /**
     * @ORM\OneToMany(targetEntity="Reading", mappedBy="sensor", cascade={"persist", "remove"}, fetch="EXTRA_LAZY")
     * @Serializer\Expose
     *
     * @var Collection
     */
    protected $readings;


    public function __construct($temperature = 25.0, $humidity = 50.0, $updateRate = 600)
    {
        $this->presetTemperature = $temperature;
        $this->presetHumidity = $humidity;
        $this->updateRate = $updateRate;
    }

    /**
     * @param \DateTime|null $start
     * @param \DateTime|null $end
     * @return Collection
     */
    public function getReadings(\DateTime $start = null, \DateTime $end = null)
    {
        if (null !== $start) {
            $criteria = Criteria::create();
            $criteria->where(Criteria::expr()->gte('timestamp', $start));
            if (null !== $end) {
                $criteria->andWhere(Criteria::expr()->lte('timestamp', $end));
            }
            $criteria->orderBy(Array('timestamp' => Criteria::ASC));

            return $this->readings->matching($criteria);
        }

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

    /**
     *
     *
     * @return Reading
     */
    public function getLatestReading()
    {
        $criteria = Criteria::create();
        $criteria->orderBy(Array('timestamp' => Criteria::DESC))->setMaxResults(1);
        $latest = $this->readings->matching($criteria);

        return $latest[0];

    }

    /**
     * @return float
     */
    public function getPresetTemperature()
    {
        return $this->presetTemperature;
    }

    /**
     * @param float $presetTemperature
     */
    public function setPresetTemperature($presetTemperature)
    {
        $this->presetTemperature = $presetTemperature;
    }

    /**
     * @return float
     */
    public function getPresetHumidity()
    {
        return $this->presetHumidity;
    }

    /**
     * @param float $presetHumidity
     */
    public function setPresetHumidity($presetHumidity)
    {
        $this->presetHumidity = $presetHumidity;
    }

    /**
     * @return int
     */
    public function getUpdateRate()
    {
        return $this->updateRate;
    }

    /**
     * @param int $updateRate
     */
    public function setUpdateRate($updateRate)
    {
        $this->updateRate = $updateRate;
    }

    public function getDataset(\DateTime $start = null, \DateTime $end = null)
    {
        return new ReadingDataset($this, $start, $end);
    }
    
    public function isOnline()
    {
        $latest = $this->getLatestReading();
        $now = new \DateTime();

        return ($now->getTimestamp() - $latest->getTimestamp()->getTimestamp()) / 60 < 10;

    }

    public function isTooHot()
    {
        $latest = $this->getLatestReading();

        return $latest->getTemperature() > ($this->presetTemperature + 2);
    }

    public function isTooCold()
    {
        $latest = $this->getLatestReading();

        return $latest->getTemperature() < ($this->presetTemperature - 2);
    }

    public function isTooDry()
    {
        $latest = $this->getLatestReading();

        return $latest->getHumidity() < ($this->presetHumidity - 10);
    }
}
