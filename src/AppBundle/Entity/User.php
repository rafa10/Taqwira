<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
    * @var string
    *
    * @ORM\Column(name="firstname", type="string", length=255, nullable=true)
    */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255, nullable=true)
     */
    private $lastname;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Center", inversedBy="user", cascade={"persist"})
     * @ORM\JoinColumn(name="center_id", referencedColumnName="id", onDelete="CASCADE")
     *
     */
    private $center;


    /*public function __construct()
    {
        parent::__construct();
        // your own logic
    }*/


    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
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
     * @return User
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
     * Set center
     *
     * @param \AppBundle\Entity\Center $center
     *
     * @return User
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
