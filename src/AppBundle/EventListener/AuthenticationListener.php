<?php

// AuthenticationListener.php

namespace AppBundle\EventListener;

use AppBundle\Entity\AuditSecurity;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Validator\Constraints\DateTime;


class AuthenticationListener implements EventSubscriberInterface
{
    private $em;

    /**
     * LastLoginSubscriber constructor.
     * @param EntityManager $em
     */

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param InteractiveLoginEvent $event
     * @throws \Doctrine\ORM\OptimisticLockException
     */

    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        /** @var User $user */
        $auditSecurty = new AuditSecurity();

        $user = $event->getAuthenticationToken()->getUser();
        $auditSecurty->setUser($user);
        $auditSecurty->setCreatedAt(new \DateTime());
        $this->em->persist($auditSecurty);
        $this->em->flush();

        // Get config of current users and set in session ('config')
//        $request = $event->getRequest();
//        if (!$request->hasPreviousSession()) {
//            return;
//        }
//        $company = $user->getCompany();
//        $configSections = $this->em->getRepository('AppBundle:ConfigSection')->findBy(array('company' => $company));
//        $configs = $this->em->getRepository('AppBundle:Config')->findBy(array( 'configSection' => $configSections ));
//        $request->getSession()->set('config', $configs);

    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin');
    }
}