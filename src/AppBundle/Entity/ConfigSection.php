<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="config_section")
 */
class ConfigSection
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Config", mappedBy="configSection")
     * @Assert\NotBlank(message="Please provide a config")
     */
    private $config;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Center", inversedBy="configSection")
     * @ORM\JoinColumn(name="center_id", referencedColumnName="id", onDelete="CASCADE")
     * @Assert\NotBlank(message="Please provide a center")
     */
    private $center;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Section", inversedBy="configSection")
     * @ORM\JoinColumn(name="section_id", referencedColumnName="id")
     */
    private $section;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->config = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add config
     *
     * @param \AppBundle\Entity\Config $config
     *
     * @return ConfigSection
     */
    public function addConfig(\AppBundle\Entity\Config $config)
    {
        $this->config[] = $config;

        return $this;
    }

    /**
     * Remove config
     *
     * @param \AppBundle\Entity\Config $config
     */
    public function removeConfig(\AppBundle\Entity\Config $config)
    {
        $this->config->removeElement($config);
    }

    /**
     * Get config
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set section
     *
     * @param \AppBundle\Entity\Section $section
     *
     * @return ConfigSection
     */
    public function setSection(\AppBundle\Entity\Section $section = null)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * Get section
     *
     * @return \AppBundle\Entity\Section
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Set center
     *
     * @param \AppBundle\Entity\Center $center
     *
     * @return ConfigSection
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
