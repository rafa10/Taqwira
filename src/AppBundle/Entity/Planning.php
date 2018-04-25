<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="planning")
 */
class Planning
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Please provide an planning date start")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\NotBlank(message="Please provide an planning date start")
     */
    private $date_start;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\NotBlank(message="Please provide an planning date end")
     */
    private $date_end;

    /**
     * @ORM\Column(type="integer", length=2, nullable=true)
     */
    private $nb_session;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Field", inversedBy="planning")
     * @ORM\JoinColumn(name="field_id", referencedColumnName="id", onDelete="CASCADE")
     * @Assert\NotBlank(message="Please provide field")
     */
    private $field;

    
    /**
     * Constructor
     */
    public function __construct()
    {
//        $this->user = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->description;
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
     * Set dateStart
     *
     * @param \DateTime $dateStart
     *
     * @return Planning
     */
    public function setDateStart($dateStart)
    {
        $this->date_start = $dateStart;

        return $this;
    }

    /**
     * Get dateStart
     *
     * @return \DateTime
     */
    public function getDateStart()
    {
        return $this->date_start;
    }

    /**
     * Set dateEnd
     *
     * @param \DateTime $dateEnd
     *
     * @return Planning
     */
    public function setDateEnd($dateEnd)
    {
        $this->date_end = $dateEnd;

        return $this;
    }

    /**
     * Get dateEnd
     *
     * @return \DateTime
     */
    public function getDateEnd()
    {
        return $this->date_end;
    }

    /**
     * Set nbSession
     *
     * @param integer $nbSession
     *
     * @return Planning
     */
    public function setNbSession($nbSession)
    {
        $this->nb_session = $nbSession;

        return $this;
    }

    /**
     * Get nbSession
     *
     * @return integer
     */
    public function getNbSession()
    {
        return $this->nb_session;
    }


    /**
     * Set field
     *
     * @param \AppBundle\Entity\Field $field
     *
     * @return Planning
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

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Planning
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
}
