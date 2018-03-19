<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="effective")
 * @ORM\HasLifecycleCallbacks
 */
class Effective
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
     * @ORM\Column(type="date", nullable=true)
     * @Assert\NotBlank(message="Please provide an dob")
     */
    private $dob;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\NotBlank(message="Please provide an email")
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Assert\NotBlank(message="Please provide a phone")
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Please provide a address")
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\NotBlank(message="Please provide an function")
     */
    private $function;

    /**
     * @ORM\Column(type="integer", length=3, nullable=true)
     * @Assert\NotBlank(message="Please provide an function")
     */
    private $weight;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\NotBlank(message="Please provide an function")
     */
    private $size;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\NotBlank(message="Please provide an function")
     */
    private $school_level;

    /**
     * @ORM\Column(type="text", length=100, nullable=true)
     * @Assert\NotBlank(message="Please provide an function")
     */
    private $educational_establishment;

    // ..........

    /**
     * @var datetime $created
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @var datetime $updated
     * @ORM\Column(type="datetime", nullable = true)
     */
    protected $updated;

    /**
     * Gets triggered only on insert
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->created = new \DateTime("now");
    }

    /**
     * Gets triggered every time on update
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updated = new \DateTime("now");
    }

    // ..........

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Center", inversedBy="effective")
     * @ORM\JoinColumn(name="center_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $center;

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
     * @return Effective
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
     * @return Effective
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
     * @return Effective
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
     * Set dob
     *
     * @param \DateTime $dob
     *
     * @return Effective
     */
    public function setDob($dob)
    {
        $this->dob = $dob;

        return $this;
    }

    /**
     * Get dob
     *
     * @return \DateTime
     */
    public function getDob()
    {
        return $this->dob;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Effective
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
     * @return Effective
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
     * Set address
     *
     * @param string $address
     *
     * @return Effective
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set function
     *
     * @param string $function
     *
     * @return Effective
     */
    public function setFunction($function)
    {
        $this->function = $function;

        return $this;
    }

    /**
     * Get function
     *
     * @return string
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     *
     * @return Effective
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return integer
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set size
     *
     * @param float $size
     *
     * @return Effective
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return float
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set schoolLevel
     *
     * @param string $schoolLevel
     *
     * @return Effective
     */
    public function setSchoolLevel($schoolLevel)
    {
        $this->school_level = $schoolLevel;

        return $this;
    }

    /**
     * Get schoolLevel
     *
     * @return string
     */
    public function getSchoolLevel()
    {
        return $this->school_level;
    }

    /**
     * Set educationalEstablishment
     *
     * @param string $educationalEstablishment
     *
     * @return Effective
     */
    public function setEducationalEstablishment($educationalEstablishment)
    {
        $this->educational_establishment = $educationalEstablishment;

        return $this;
    }

    /**
     * Get educationalEstablishment
     *
     * @return string
     */
    public function getEducationalEstablishment()
    {
        return $this->educational_establishment;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Effective
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Effective
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set center
     *
     * @param \AppBundle\Entity\Center $center
     *
     * @return Effective
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
