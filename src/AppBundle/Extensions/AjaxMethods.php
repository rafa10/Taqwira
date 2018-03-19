<?php


namespace AppBundle\Extensions;


use AppBundle\Entity\Company;
use AppBundle\Entity\Field;
use AppBundle\Entity\Group;
use AppBundle\Entity\GroupScenario;
use AppBundle\Entity\Role;
use AppBundle\Entity\Scenario;
use AppBundle\Entity\Session;
use AppBundle\Entity\User;
use AppBundle\Entity\UserGroup;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class AjaxMethods
 * @package AppBundle\Extensions
 */
class AjaxMethods{
    protected $em;
    private $container;

    // We need to inject this variables later.
    public function __construct(EntityManager $entityManager, Container $container)
    {
        $this->em = $entityManager;
        $this->container = $container;
    }

    /**
     * List of session selected or not selected by field
     *
     * @param $fieldId
     * @return array
     */
    public function selectedTimeByField($fieldId)
    {
        /* @var Field $field */
        $field = $this->em->getRepository('AppBundle:Field')->find($fieldId);
        $center = $field->getCenter();
        /* @var Session $session */
        $fieldSession = [];
        $sessions = $this->em->getRepository('AppBundle:Session')->findBy(array('center' => $center));
        foreach ($sessions as $session) {
            $fieldSession[] = [
                'id' => $session->getTimeStart()->format('H:i'),
                'name' => $session->getTimeStart()->format('H:i'),
            ];
        }

        return $fieldSession;
    }
}


