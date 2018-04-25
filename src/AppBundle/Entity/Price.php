<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="price")
 */
class Price
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\NotBlank(message="Please provide an amount")
     */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookingType", inversedBy="price")
     * @ORM\JoinColumn(name="booking_type_id", referencedColumnName="id", onDelete="CASCADE")
     * @Assert\NotBlank(message="Please provide booking type")
     */
    private $bookingType;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Field", inversedBy="price")
     * @ORM\JoinColumn(name="field_id", referencedColumnName="id", onDelete="CASCADE")
     * @Assert\NotBlank(message="Please provide field")
     */
    private $field;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Session", mappedBy="price")
     * @Assert\NotBlank(message="Please provide session")
     */
    private $session;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Day", mappedBy="price")
     * @Assert\NotBlank(message="Please provide day")
     */
    private $day;

    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->session = new \Doctrine\Common\Collections\ArrayCollection();
        $this->day = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->amount;
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
     * Set amount
     *
     * @param float $amount
     *
     * @return Price
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set bookingType
     *
     * @param \AppBundle\Entity\BookingType $bookingType
     *
     * @return Price
     */
    public function setBookingType(\AppBundle\Entity\BookingType $bookingType = null)
    {
        $this->bookingType = $bookingType;

        return $this;
    }

    /**
     * Get bookingType
     *
     * @return \AppBundle\Entity\BookingType
     */
    public function getBookingType()
    {
        return $this->bookingType;
    }

    /**
     * Set center
     *
     * @param \AppBundle\Entity\Center $center
     *
     * @return Price
     */
    public function setCenter(\AppBundle\Entity\Center $center = null)
    {
        $this->center = $center;

        return $this;
    }

    /**
     * Get center
     *
     * @return \AppBundle\Entity\Center
     */
    public function getCenter()
    {
        return $this->center;
    }

    /**
     * Add session
     *
     * @param \AppBundle\Entity\Session $session
     *
     * @return Price
     */
    public function addSession(\AppBundle\Entity\Session $session)
    {
        $this->session[] = $session;

        return $this;
    }

    /**
     * Remove session
     *
     * @param \AppBundle\Entity\Session $session
     */
    public function removeSession(\AppBundle\Entity\Session $session)
    {
        $this->session->removeElement($session);
    }

    /**
     * Get session
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Add day
     *
     * @param \AppBundle\Entity\Day $day
     *
     * @return Price
     */
    public function addDay(\AppBundle\Entity\Day $day)
    {
        $this->day[] = $day;

        return $this;
    }

    /**
     * Remove day
     *
     * @param \AppBundle\Entity\Day $day
     */
    public function removeDay(\AppBundle\Entity\Day $day)
    {
        $this->day->removeElement($day);
    }

    /**
     * Get day
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Set field.
     *
     * @param \AppBundle\Entity\Field|null $field
     *
     * @return Price
     */
    public function setField(\AppBundle\Entity\Field $field = null)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Get field.
     *
     * @return \AppBundle\Entity\Field|null
     */
    public function getField()
    {
        return $this->field;
    }
}
