<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="section")
 */
class Section
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ConfigSection", mappedBy="section")
     */
    private $configSection;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->configSection = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set description
     *
     * @param string $description
     *
     * @return Section
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
     * Add configSection
     *
     * @param \AppBundle\Entity\ConfigSection $configSection
     *
     * @return Section
     */
    public function addConfigSection(\AppBundle\Entity\ConfigSection $configSection)
    {
        $this->configSection[] = $configSection;

        return $this;
    }

    /**
     * Remove configSection
     *
     * @param \AppBundle\Entity\ConfigSection $configSection
     */
    public function removeConfigSection(\AppBundle\Entity\ConfigSection $configSection)
    {
        $this->configSection->removeElement($configSection);
    }

    /**
     * Get configSection
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConfigSection()
    {
        return $this->configSection;
    }
}
