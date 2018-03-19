<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping AS ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\MaxDepth;

/**
 * @ORM\Entity
 * @ORM\Table(name="day")
 */
class Day
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\NotBlank(message="Please provide an booking type description")
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Price", inversedBy="day")
     * @ORM\JoinTable(
     *     name="price_day",
     *     joinColumns={@ORM\JoinColumn(name="day_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="price_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")}
     * )
     * @MaxDepth(0)
     */
    private $price;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Program", inversedBy="day")
     * @ORM\JoinTable(
     *     name="program_day",
     *     joinColumns={@ORM\JoinColumn(name="day_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")},
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
        $this->price =new ArrayCollection();
        $this->program =new ArrayCollection();
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
     * @return Day
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
     * Add price
     *
     * @param \AppBundle\Entity\Price $price
     *
     * @return Day
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
     * @return Day
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
