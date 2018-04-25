<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping AS ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="field")
 */
class Field
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Please provide an field name")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=300, nullable=true)
     * @Assert\NotBlank(message="Please provide an field description")
     */
    private $description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank(message="Please provide an field capacity")
     */
    private $capacity;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Center", inversedBy="field")
     * @ORM\JoinColumn(name="center_id", referencedColumnName="id", onDelete="CASCADE")
     *
     */
    private $center;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Session", mappedBy="field")
     * @Assert\NotBlank(message="Please provide an field session")
     */
    private $session;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Planning", mappedBy="field")
     */
    private $planning;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Booking", mappedBy="field")
     */
    private $booking;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Price", mappedBy="field")
     */
    private $price;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Program", mappedBy="field")
     */
    private $program;

    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->session = new ArrayCollection();
        $this->planning = new ArrayCollection();
        $this->booking = new ArrayCollection();
        $this->price = new ArrayCollection();
        $this->program = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->name;
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
     * @return Field
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
     * Set description
     *
     * @param string $description
     *
     * @return Field
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set capacity
     *
     * @param integer $capacity
     *
     * @return Field
     */
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;

        return $this;
    }

    /**
     * Get capacity
     *
     * @return integer
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * Set center
     *
     * @param \AppBundle\Entity\Center $center
     *
     * @return Field
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
     * Add planning
     *
     * @param \AppBundle\Entity\Planning $planning
     *
     * @return Field
     */
    public function addPlanning(\AppBundle\Entity\Planning $planning)
    {
        $this->planning[] = $planning;

        return $this;
    }

    /**
     * Remove planning
     *
     * @param \AppBundle\Entity\Planning $planning
     */
    public function removePlanning(\AppBundle\Entity\Planning $planning)
    {
        $this->planning->removeElement($planning);
    }

    /**
     * Get planning
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPlanning()
    {
        return $this->planning;
    }

    /**
     * Add booking
     *
     * @param \AppBundle\Entity\Booking $booking
     *
     * @return Field
     */
    public function addBooking(\AppBundle\Entity\Booking $booking)
    {
        $this->booking[] = $booking;

        return $this;
    }

    /**
     * Remove booking
     *
     * @param \AppBundle\Entity\Booking $booking
     */
    public function removeBooking(\AppBundle\Entity\Booking $booking)
    {
        $this->booking->removeElement($booking);
    }

    /**
     * Get booking
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBooking()
    {
        return $this->booking;
    }


    /**
     * Add session
     *
     * @param \AppBundle\Entity\Session $session
     *
     * @return Field
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
     * Add program
     *
     * @param \AppBundle\Entity\Program $program
     *
     * @return Field
     */
    public function addProgram(\AppBundle\Entity\Program $program)
    {
        $this->program[] = $program;

        return $this;
    }

    /**
     * Remove program
     *
     * @param \AppBundle\Entity\Program $program
     */
    public function removeProgram(\AppBundle\Entity\Program $program)
    {
        $this->program->removeElement($program);
    }

    /**
     * Get program
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProgram()
    {
        return $this->program;
    }

    /**
     * Add price.
     *
     * @param \AppBundle\Entity\Price $price
     *
     * @return Field
     */
    public function addPrice(\AppBundle\Entity\Price $price)
    {
        $this->price[] = $price;

        return $this;
    }

    /**
     * Remove price.
     *
     * @param \AppBundle\Entity\Price $price
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removePrice(\AppBundle\Entity\Price $price)
    {
        return $this->price->removeElement($price);
    }

    /**
     * Get price.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPrice()
    {
        return $this->price;
    }
}
