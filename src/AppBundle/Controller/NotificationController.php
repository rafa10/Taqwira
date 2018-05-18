<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * Calendar Controller
 *
 * Class CalendarController
 * @Route("/notification")
 * @package AppBundle\Controller
 */
class NotificationController extends Controller
{

    /**
     * Display Notification.
     * @Route("/show", name="notification_show")
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN') or has_role('ROLE_USER') or has_role('ROLE_USER')")
     * @Method("GET")
     */
    public function getNotificationAction()
    {
        $em = $this->getDoctrine()->getManager();

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $notifications = $em->getRepository('AppBundle:Notification')->findBy(array('center' => null), array('created' => 'DESC'));

        } else {
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $center = $userLogin->getCenter();
            $notifications = $em->getRepository('AppBundle:Notification')->findBy(array('center' => $center), array('created' => 'DESC'));

        }

        $payload=array();
        $payload['status']='ok';
        $payload['page']='show';
        $payload['length']= count($notifications);
        $payload['html'] = $this->renderView('notification/index.html.twig', array(
            'notifications' => $notifications
        ));
        return new Response(json_encode($payload));
    }

    /**
     * Delete notification
     * @Route("/delete/{id}", name="notification_delete")
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN') or has_role('ROLE_USER') or has_role('ROLE_USER')")
     * @Method("GET")
     * @param Notification $notification
     * @return Response
     */
    public function deleteAction(Notification $notification)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($notification);
        $em->flush();

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $notifications = $em->getRepository('AppBundle:Notification')->findBy(array('center' => null), array('created' => 'DESC'));

        } else {
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $center = $userLogin->getCenter();
            $notifications = $em->getRepository('AppBundle:Notification')->findBy(array('center' => $center), array('created' => 'DESC'));

        }

        $payload=array();
        $payload['status']='ok';
        $payload['page']='refresh';
        $payload['length']= count($notifications);
        return new Response(json_encode($payload));
    }

    //==================================================================================================================
    // Notification pannier ============================================================================================
    //==================================================================================================================

    /**
     * Display basket subscription.
     * @Route("/basket/show", name="basket_show")
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN') or has_role('ROLE_USER') or has_role('ROLE_USER')")
     * @Method("GET")
     */
    public function getBasketsAction()
    {
        $payload=array();
        $payload['status']='ok';
        $payload['page']='show';
        $payload['html'] = $this->renderView('notification/baskets.html.twig');
        return new Response(json_encode($payload));
    }

}
