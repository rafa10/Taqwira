<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="center")
 * @ORM\HasLifecycleCallbacks
 */
class Center
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Please provide an center name")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Please provide an center adresse")
     */
    private $address;

    /**
     * @ORM\Column(type="integer", length=4, nullable=true)
     * @Assert\NotBlank(message="Please provide an center postal code")
     * @Assert\Length(min="4", max="4")
     */
    private $cp;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     * @Assert\NotBlank(message="Please provide a phone")
     * @Assert\Length(min="8", max="8")
     * @Assert\Regex(pattern="/^[0-9]*$/")
     */
    private $phone;

    /**
     * @ORM\Column(type="boolean", length=1, nullable=true)
     */
    private $is_active;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $color;

    /**
     * @ORM\Column(type="boolean", length=1, nullable=true)
     */
    private $share_program;

    // ..........

    /**
     * @var datetime $created
     *
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @var datetime $updated
     *
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Region", inversedBy="center")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $region;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\City", inversedBy="center")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $city;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\User", mappedBy="center")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Field", mappedBy="center")
     */
    private $field;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Session", mappedBy="center")
     */
    private $session;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Customer", mappedBy="center")
     */
    private $customer;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Image", mappedBy="center")
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Event", mappedBy="center")
     */
    private $event;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Effective", mappedBy="center")
     */
    private $effective;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ConfigSection", mappedBy="center")
     */
    private $configSection;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Bill", mappedBy="center")
     */
    private $bill;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Service", mappedBy="center")
     */
    private $service;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Notification", mappedBy="center")
     */
    private $notification;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->user = new \Doctrine\Common\Collections\ArrayCollection();
        $this->field = new \Doctrine\Common\Collections\ArrayCollection();
        $this->session = new \Doctrine\Common\Collections\ArrayCollection();
        $this->customer = new \Doctrine\Common\Collections\ArrayCollection();
        $this->image = new \Doctrine\Common\Collections\ArrayCollection();
        $this->event = new \Doctrine\Common\Collections\ArrayCollection();
        $this->effective = new \Doctrine\Common\Collections\ArrayCollection();
        $this->configSection = new \Doctrine\Common\Collections\ArrayCollection();
        $this->bill = new \Doctrine\Common\Collections\ArrayCollection();
        $this->service = new \Doctrine\Common\Collections\ArrayCollection();
        $this->notification = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Center
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Center
     */
    public function setIsActive($isActive)
    {
        $this->is_active = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->is_active;
    }

    /**
     * Add user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Center
     */
    public function addUser(\AppBundle\Entity\User $user)
    {
        $this->user[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \AppBundle\Entity\User $user
     */
    public function removeUser(\AppBundle\Entity\User $user)
    {
        $this->user->removeElement($user);
    }

    /**
     * Get user
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add field
     *
     * @param \AppBundle\Entity\Field $field
     *
     * @return Center
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
     * Add session
     *
     * @param \AppBundle\Entity\Session $session
     *
     * @return Center
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
     * Add customer
     *
     * @param \AppBundle\Entity\Customer $customer
     *
     * @return Center
     */
    public function addCustomer(\AppBundle\Entity\Customer $customer)
    {
        $this->customer[] = $customer;

        return $this;
    }

    /**
     * Remove customer
     *
     * @param \AppBundle\Entity\Customer $customer
     */
    public function removeCustomer(\AppBundle\Entity\Customer $customer)
    {
        $this->customer->removeElement($customer);
    }

    /**
     * Get customer
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Center
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
     * @return Center
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
     * Set avatar
     *
     * @param string $avatar
     *
     * @return Center
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Add image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return Center
     */
    public function addImage(\AppBundle\Entity\Image $image)
    {
        $this->image[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param \AppBundle\Entity\Image $image
     */
    public function removeImage(\AppBundle\Entity\Image $image)
    {
        $this->image->removeElement($image);
    }

    /**
     * Get image
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set color
     *
     * @param string $color
     *
     * @return Center
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Center
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
     * Set cp
     *
     * @param integer $cp
     *
     * @return Center
     */
    public function setCp($cp)
    {
        $this->cp = $cp;

        return $this;
    }

    /**
     * Get cp
     *
     * @return integer
     */
    public function getCp()
    {
        return $this->cp;
    }

    /**
     * Set region
     *
     * @param \AppBundle\Entity\Region $region
     *
     * @return Center
     */
    public function setRegion(\AppBundle\Entity\Region $region = null)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return \AppBundle\Entity\Region
     */
    public function getRegion()
    {
        return $this->region;
    }



    /**
     * Set city
     *
     * @param \AppBundle\Entity\City $city
     *
     * @return Center
     */
    public function setCity(\AppBundle\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return \AppBundle\Entity\City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Add event
     *
     * @param \AppBundle\Entity\Event $event
     *
     * @return Center
     */
    public function addEvent(\AppBundle\Entity\Event $event)
    {
        $this->event[] = $event;

        return $this;
    }

    /**
     * Remove event
     *
     * @param \AppBundle\Entity\Event $event
     */
    public function removeEvent(\AppBundle\Entity\Event $event)
    {
        $this->event->removeElement($event);
    }

    /**
     * Get event
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Add effective
     *
     * @param \AppBundle\Entity\Effective $effective
     *
     * @return Center
     */
    public function addEffective(\AppBundle\Entity\Effective $effective)
    {
        $this->effective[] = $effective;

        return $this;
    }

    /**
     * Remove effective
     *
     * @param \AppBundle\Entity\Effective $effective
     */
    public function removeEffective(\AppBundle\Entity\Effective $effective)
    {
        $this->effective->removeElement($effective);
    }

    /**
     * Get effective
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEffective()
    {
        return $this->effective;
    }

    /**
     * Add configSection
     *
     * @param \AppBundle\Entity\ConfigSection $configSection
     *
     * @return Center
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

    /**
     * Set shareProgram
     *
     * @param boolean $shareProgram
     *
     * @return Center
     */
    public function setShareProgram($shareProgram)
    {
        $this->share_program = $shareProgram;

        return $this;
    }

    /**
     * Get shareProgram
     *
     * @return boolean
     */
    public function getShareProgram()
    {
        return $this->share_program;
    }

    /**
     * Add bill
     *
     * @param \AppBundle\Entity\Bill $bill
     *
     * @return Center
     */
    public function addBill(\AppBundle\Entity\Bill $bill)
    {
        $this->bill[] = $bill;

        return $this;
    }

    /**
     * Remove bill
     *
     * @param \AppBundle\Entity\Bill $bill
     */
    public function removeBill(\AppBundle\Entity\Bill $bill)
    {
        $this->bill->removeElement($bill);
    }

    /**
     * Get bill
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBill()
    {
        return $this->bill;
    }

    /**
     * Add service
     *
     * @param \AppBundle\Entity\Service $service
     *
     * @return Center
     */
    public function addService(\AppBundle\Entity\Service $service)
    {
        $this->service[] = $service;

        return $this;
    }

    /**
     * Remove service
     *
     * @param \AppBundle\Entity\Service $service
     */
    public function removeService(\AppBundle\Entity\Service $service)
    {
        $this->service->removeElement($service);
    }

    /**
     * Get service
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Center
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
     * Add notification.
     *
     * @param \AppBundle\Entity\Notification $notification
     *
     * @return Center
     */
    public function addNotification(\AppBundle\Entity\Notification $notification)
    {
        $this->notification[] = $notification;

        return $this;
    }

    /**
     * Remove notification.
     *
     * @param \AppBundle\Entity\Notification $notification
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeNotification(\AppBundle\Entity\Notification $notification)
    {
        return $this->notification->removeElement($notification);
    }

    /**
     * Get notification.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotification()
    {
        return $this->notification;
    }
}
