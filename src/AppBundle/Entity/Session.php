<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;
use JMS\Serializer\Annotation\MaxDepth;

/**
 * @ORM\Entity
 * @ORM\Table(name="session")
 */
class Session
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @JMS\Type("DateTime<'H:i'>")
     * @ORM\Column(type="time", nullable=true)
     * @Assert\NotBlank(message="Please provide an session duration")
     */
    private $time_start;

    /**
     * @JMS\Type("DateTime<'H:i'>")
     * @ORM\Column(type="time", nullable=true)
     * @Assert\NotBlank(message="Please provide an session duration")
     */
    private $time_end;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Center", inversedBy="session")
     * @ORM\JoinColumn(name="center_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $center;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Field", inversedBy="session")
     * @ORM\JoinTable(
     *     name="field_session",
     *     joinColumns={@ORM\JoinColumn(name="session_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="field_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")}
     * )
     * @MaxDepth(0)
     */
    private $field;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Price", inversedBy="session")
     * @ORM\JoinTable(
     *     name="price_session",
     *     joinColumns={@ORM\JoinColumn(name="session_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="price_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")}
     * )
     * @MaxDepth(0)
     */
    private $price;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Program", inversedBy="session")
     * @ORM\JoinTable(
     *     name="program_session",
     *     joinColumns={@ORM\JoinColumn(name="session_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="program_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")}
     * )
     * @MaxDepth(0)
     */
    private $program;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->field = new \Doctrine\Common\Collections\ArrayCollection();
        $this->price = new \Doctrine\Common\Collections\ArrayCollection();
        $this->program = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->time_start->format('H:i');
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
     * Set timeStart
     *
     * @param \DateTime $timeStart
     *
     * @return Session
     */
    public function setTimeStart($timeStart)
    {
        $this->time_start = $timeStart;

        return $this;
    }

    /**
     * Get timeStart
     *
     * @return \DateTime
     */
    public function getTimeStart()
    {
        return $this->time_start;
    }

    /**
     * Set timeEnd
     *
     * @param \DateTime $timeEnd
     *
     * @return Session
     */
    public function setTimeEnd($timeEnd)
    {
        $this->time_end = $timeEnd;

        return $this;
    }

    /**
     * Get timeEnd
     *
     * @return \DateTime
     */
    public function getTimeEnd()
    {
        return $this->time_end;
    }



    /**
     * Add field
     *
     * @param \AppBundle\Entity\Field $field
     *
     * @return Session
     */
    public function addField(\AppBundle\Entity\Field $field)
    {
        $this->field[] = $field;

        return $this;
    }

    /**
     * Remove field
     *
     * @param \AppBundle\Entity\Field $field
     */
    public function removeField(\AppBundle\Entity\Field $field)
    {
        $this->field->removeElement($field);
    }

    /**
     * Get field
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set center
     *
     * @param \AppBundle\Entity\Center $center
     *
     * @return Session
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
     * Add price
     *
     * @param \AppBundle\Entity\Price $price
     *
     * @return Session
     */
    public function addPrice(\AppBundle\Entity\Price $price)
    {
        $this->price[] = $price;

        return $this;
    }

    /**
     * Remove price
     *
     * @param \AppBundle\Entity\Price $price
     */
    public function removePrice(\AppBundle\Entity\Price $price)
    {
        $this->price->removeElement($price);
    }

    /**
     * Get price
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPrice()
    {
        return $this->price;
    }
    

    /**
     * Add program
     *
     * @param \AppBundle\Entity\Program $program
     *
     * @return Session
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
}
