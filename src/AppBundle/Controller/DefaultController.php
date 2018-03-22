<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $fields = $em->getRepository('AppBundle:Field')->findAll();
            $customers = $em->getRepository('AppBundle:Customer')->findAll();
            $users = $em->getRepository('AppBundle:User')->findAll();
            $match = $em->getRepository('AppBundle:BookingType')->findBy(array('description' => 'Match'));
            $matchs = $em->getRepository('AppBundle:Booking')->findBy(array('bookingType' => $match));
            $subscription = $em->getRepository('AppBundle:BookingType')->findBy(array('description' => 'Abonnement'));
            $subscriptions = $em->getRepository('AppBundle:Booking')->findBy(array('bookingType' => $subscription));
            // last 5 bookings =============================================================================================
            $lastbookings = $em->createQuery(' SELECT j FROM AppBundle:Booking b ORDER BY b.date DESC')
                ->setMaxResults(5)
                ->getResult();

        } else {
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $center = $userLogin->getCenter();
            $fields = $em->getRepository('AppBundle:Field')->findBy(array('center' => $center));
            $customers = $em->getRepository('AppBundle:Customer')->findBy(array('center' => $center));
            $match = $em->getRepository('AppBundle:BookingType')->findBy(array('description' => 'Match'));
            $matchs = $em->getRepository('AppBundle:Booking')->findBy(array('field' => $fields ,'bookingType' => $match));
            $subscription = $em->getRepository('AppBundle:BookingType')->findBy(array('description' => 'Abonnement'));
            $subscriptions = $em->getRepository('AppBundle:Booking')->findBy(array('field' => $fields,'bookingType' => $subscription));
            // last 5 bookings =============================================================================================
            $lastbookings = $em->getRepository('AppBundle:Booking')->findBy(array('field'=> $fields, 'bookingType' => $match), array('date' => 'DESC'), 5);

        }

        return $this->render('default/index.html.twig', array(
            'fields' => $fields,
            'customers' => $customers,
            'matchs' => $matchs,
            'subscriptions' => $subscriptions,
            'last_bookings' => $lastbookings,

        ));
    }

    /**
     * Display basket subscription.
     * @Route("/basket/show", name="basket_show")
     * @Method("GET")
     */
    public function displayBaskets()
    {
        $payload=array();
        $payload['status']='ok';
        $payload['page']='show';
        $payload['html'] = $this->renderView('default/baskets.html.twig');
        return new Response(json_encode($payload));
    }

    /**
     * Calculated nb total booking of current user
     * @Route("/booking/chart", name="booking_chart")
     * @Method("GET")
     * @return Response
     */
    public function getBookingMonthly()
    {
        $jan = 0; $feb = 0; $mar = 0; $apr = 0; $may = 0; $jun = 0; $jul = 0; $aug = 0; $sep = 0; $oct = 0; $nov = 0; $dec = 0;

        $payload=array();
        $payload['status']='ok';
        $payload['page']='show';
        $payload['match'] = [$jan, $feb, $mar, $apr, $may, $jun, $jul, $aug, $sep, $oct, $nov, $dec];
        $payload['abonnement'] = [$jan, $feb, $mar, $apr, $may, $jun, $jul, $aug, $sep, $oct, $nov, $dec];

        return new Response(json_encode($payload));

    }

    /**
     * Calculated nb total sessions of current user
     * @Route("/session/chart", name="session_show")
     * @Method("GET")
     * @return Response
     */
    public function getSessionMonthly()
    {
        $jan = 0; $feb = 0; $mar = 0; $apr = 0; $may = 0; $jun = 0; $jul = 0; $aug = 0; $sep = 0; $oct = 0; $nov = 0; $dec = 0;
        $em = $this->getDoctrine()->getManager();

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $tab_audit = [];
            $auditsecurity = [];
            $users = $em->getRepository('AppBundle:User')->findAll();

            foreach ($users as $user){
                $tab_audit[] = $em->getRepository('AppBundle:AuditSecurity')->findBy(array('user' => $user));
            }
            foreach ($tab_audit as $item){
                foreach ($item as $audit){
                    $auditsecurity[] = $audit;
                }
            }

        } else {
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $tab_audit[] = $em->getRepository('AppBundle:AuditSecurity')->findBy(array('user' => $userLogin));
            foreach ($tab_audit as $item){
                foreach ($item as $audit){
                    $auditsecurity[] = $audit;
                }
            }

        }

        foreach ( $auditsecurity as $value){
            if ($value->getCreatedAt()->format('Y-m') == date('Y-01')) { $jan = $jan + 1; }
            if ($value->getCreatedAt()->format('Y-m') == date('Y-02')) { $feb = $feb + 1; }
            if ($value->getCreatedAt()->format('Y-m') == date('Y-03')) { $mar = $mar + 1; }
            if ($value->getCreatedAt()->format('Y-m') == date('Y-04')) { $apr = $apr + 1; }
            if ($value->getCreatedAt()->format('Y-m') == date('Y-05')) { $may = $may + 1; }
            if ($value->getCreatedAt()->format('Y-m') == date('Y-06')) { $jun = $jun + 1; }
            if ($value->getCreatedAt()->format('Y-m') == date('Y-07')) { $jul = $jul + 1; }
            if ($value->getCreatedAt()->format('Y-m') == date('Y-08')) { $aug = $aug + 1; }
            if ($value->getCreatedAt()->format('Y-m') == date('Y-09')) { $sep = $sep + 1; }
            if ($value->getCreatedAt()->format('Y-m') == date('Y-10')) { $oct = $oct + 1; }
            if ($value->getCreatedAt()->format('Y-m') == date('Y-11')) { $nov = $nov + 1; }
            if ($value->getCreatedAt()->format('Y-m') == date('Y-12')) { $dec = $dec + 1; }
        }

        $payload=array();
        $payload['status']='ok';
        $payload['page']='show';
        $payload['session'] = [$jan, $feb, $mar, $apr, $may, $jun, $jul, $aug, $sep, $oct, $nov, $dec];

        return new Response(json_encode($payload));
    }

    /**
     * Calculated nb total sessions of current user
     * @Route("/booking_type/chart", name="booking_type_show")
     * @Method("GET")
     * @return Response
     */
    public function getTotalBookingType()
    {
        $em = $this->getDoctrine()->getManager();

        $matchType = $em->getRepository('AppBundle:BookingType')->findBy(array('description' => 'Match'));
        $subscriptionType = $em->getRepository('AppBundle:BookingType')->findBy(array('description' => 'Abonnement'));

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $matchs = $em->getRepository('AppBundle:Booking')->findBy(array('bookingType' => $matchType));
            $subscriptions = $em->getRepository('AppBundle:Booking')->findBy(array('bookingType' => $subscriptionType));

        } else {
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $center = $userLogin->getCenter();
            $fields = $em->getRepository('AppBundle:Field')->findBy(array('center' => $center));
            $matchs = $em->getRepository('AppBundle:Booking')->findBy(array('field' => $fields ,'bookingType' => $matchType));;
            $subscriptions = $em->getRepository('AppBundle:Booking')->findBy(array('field' => $fields,'bookingType' => $subscriptionType));

        }

        $match = count($matchs);
        $abonnement = count($subscriptions);

        $payload=array();
        $payload['status']='ok';
        $payload['page']='show';
        $payload['match'] = $match;
        $payload['abonnement'] = $abonnement;

        return new Response(json_encode($payload));
    }

}
