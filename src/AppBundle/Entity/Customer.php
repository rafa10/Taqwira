<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="customer")
 */
class Customer
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Please provide a firstname")
     * @Assert\Regex(
     *     pattern     = "/^[a-zàâçéèêëîïôûùüÿñæœ .-]*$/i",
     *     htmlPattern = "^[a-zA-Z]+$"
     * )
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Please provide a lastname")
     * @Assert\Regex(
     *     pattern     = "/^[a-zàâçéèêëîïôûùüÿñæœ .-]*$/i",
     *     htmlPattern = "^[a-zA-Z]+$"
     * )
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $sexe;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\NotBlank(message="Please provide an email")
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=8, nullable=true)
     * @Assert\NotBlank(message="Please provide a phone")
     * @Assert\Regex(pattern="/^[0-9]*$/")
     */
    private $phone;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Booking", mappedBy="customer")
     */
    private $booking;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Center", inversedBy="customer")
     * @ORM\JoinColumn(name="center_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $center;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->booking = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->email;
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
     * Set firstname
     *
     * @param string $firstname
     *
     * @return Customer
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return Customer
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set sexe
     *
     * @param string $sexe
     *
     * @return Customer
     */
    public function setSexe($sexe)
    {
        $this->sexe = $sexe;

        return $this;
    }

    /**
     * Get sexe
     *
     * @return string
     */
    public function getSexe()
    {
        return $this->sexe;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Customer
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Customer
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }


    /**
     * Add booking
     *
     * @param \AppBundle\Entity\Booking $booking
     *
     * @return Customer
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
     * Set center
     *
     * @param \AppBundle\Entity\Center $center
     *
     * @return Customer
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
}
