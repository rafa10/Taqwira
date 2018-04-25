<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="program")
 */
class Program
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookingType", inversedBy="program")
     * @ORM\JoinColumn(name="booking_type_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $bookingType;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Field", inversedBy="program")
     * @ORM\JoinColumn(name="field_id", referencedColumnName="id", onDelete="CASCADE")
     * @Assert\NotBlank(message="Please provide field")
     */
    private $field;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Session", mappedBy="program")
     * @Assert\NotBlank(message="Please provide session")
     */
    private $session;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Day", mappedBy="program")
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
//        return $this->;
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
     * Set bookingType
     *
     * @param \AppBundle\Entity\BookingType $bookingType
     *
     * @return Program
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
     * Add session
     *
     * @param \AppBundle\Entity\Session $session
     *
     * @return Program
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
     * @return Program
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
     * Set field
     *
     * @param \AppBundle\Entity\Field $field
     *
     * @return Program
     */
    public function setField(\AppBundle\Entity\Field $field = null)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Get field
     *
     * @return \AppBundle\Entity\Field
     */
    public function getField()
    {
        return $this->field;
    }
}
