<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="city")
 */
class City
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @Assert\NotBlank(message="Please provide an center name")
     */
    private $name;

    // ..........

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Region", inversedBy="city")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $region;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Center", mappedBy="city")
     */
    private $center;

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->center = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return City
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set region
     *
     * @param \AppBundle\Entity\Region $region
     *
     * @return City
     */
    public function setRegion(\AppBundle\Entity\Region $region = null)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return \AppBundle\Entity\Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Add center
     *
     * @param \AppBundle\Entity\Center $center
     *
     * @return City
     */
    public function addCenter(\AppBundle\Entity\Center $center)
    {
        $this->center[] = $center;

        return $this;
    }

    /**
     * Remove center
     *
     * @param \AppBundle\Entity\Center $center
     */
    public function removeCenter(\AppBundle\Entity\Center $center)
    {
        $this->center->removeElement($center);
    }

    /**
     * Get center
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCenter()
    {
        return $this->center;
    }
}
