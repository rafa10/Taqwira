<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="config")
 */
class Config
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $cle;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $valeur;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ConfigSection", inversedBy="config")
     * @ORM\JoinColumn(name="config_section_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $configSection;

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
     * Set cle
     *
     * @param string $cle
     *
     * @return Config
     */
    public function setCle($cle)
    {
        $this->cle = $cle;

        return $this;
    }

    /**
     * Get cle
     *
     * @return string
     */
    public function getCle()
    {
        return $this->cle;
    }

    /**
     * Set valeur
     *
     * @param string $valeur
     *
     * @return Config
     */
    public function setValeur($valeur)
    {
        $this->valeur = $valeur;

        return $this;
    }

    /**
     * Get valeur
     *
     * @return string
     */
    public function getValeur()
    {
        return $this->valeur;
    }

    /**
     * Set configSection
     *
     * @param \AppBundle\Entity\ConfigSection $configSection
     *
     * @return Config
     */
    public function setConfigSection(\AppBundle\Entity\ConfigSection $configSection = null)
    {
        $this->configSection = $configSection;

        return $this;
    }

    /**
     * Get configSection
     *
     * @return \AppBundle\Entity\ConfigSection
     */
    public function getConfigSection()
    {
        return $this->configSection;
    }
}
