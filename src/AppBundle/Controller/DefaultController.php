<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BookingType;
use AppBundle\Entity\Region;
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
    public function indexAction()
    {
        $centers = null;
        $lastBookings = null;
        $toDayBookings = null;
        $now = new \DateTime('now');
        $now->setTime(00, 00);
        $em = $this->getDoctrine()->getManager();

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $fields = $em->getRepository('AppBundle:Field')->findAll();
            $customers = $em->getRepository('AppBundle:Customer')->findAll();
            $centers = $em->getRepository('AppBundle:Center')->findAll();
            $match = $em->getRepository('AppBundle:BookingType')->findBy(array('description' => 'Match'));
            $matchs = $em->getRepository('AppBundle:Booking')->findBy(array('bookingType' => $match));
            $subscription = $em->getRepository('AppBundle:BookingType')->findBy(array('description' => 'Abonnement'));
            $subscriptions = $em->getRepository('AppBundle:Booking')->findBy(array('bookingType' => $subscription));
            // last 5 bookings =========================================================================================
            $lastBookings = $em->createQuery(' SELECT b FROM AppBundle:Booking b ORDER BY b.date DESC')
                ->setMaxResults(5)
                ->getResult();
            // bookings today ==========================================================================================
            $toDayBookings = $em->getRepository('AppBundle:Booking')->findBy(array('date'=> $now), array('date' => 'ASC'));

        } else {
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $center = $userLogin->getCenter();
            $fields = $em->getRepository('AppBundle:Field')->findBy(array('center' => $center));
            $customers = $em->getRepository('AppBundle:Customer')->findBy(array('center' => $center));
            $match = $em->getRepository('AppBundle:BookingType')->findBy(array('description' => 'Match'));
            $matchs = $em->getRepository('AppBundle:Booking')->findBy(array('field' => $fields ,'bookingType' => $match));
            $subscription = $em->getRepository('AppBundle:BookingType')->findBy(array('description' => 'Abonnement'));
            $subscriptions = $em->getRepository('AppBundle:Booking')->findBy(array('field' => $fields,'bookingType' => $subscription));
            // last 5 bookings =========================================================================================
            $lastBookings = $em->getRepository('AppBundle:Booking')->findBy(array('field'=> $fields, 'bookingType' => $match), array('date' => 'DESC'), 5);
            // bookings today ==========================================================================================
            $toDayBookings = $em->getRepository('AppBundle:Booking')->findBy(array('field'=> $fields, 'date'=> $now), array('date' => 'ASC'));

        }

        return $this->render('default/index.html.twig', array(
            'fields' => $fields,
            'customers' => $customers,
            'centers' => $centers,
            'matchs' => $matchs,
            'subscriptions' => $subscriptions,
            'last_bookings' => $lastBookings,
            'toDayBookings' => $toDayBookings
        ));
    }

    /**
     * @Route("/help", name="help_index")
     * @return Response
     */
    public function helpAction()
    {
        return $this->render('default/help.html.twig', array());
    }

    /**
     * Calculated nb total booking of current user
     * @Route("/booking/chart", name="booking_chart")
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN') or has_role('ROLE_USER')")
     * @Method("GET")
     * @return Response
     */
    public function getBookingMonthly()
    {

        $jan_m = 0; $feb_m = 0; $mar_m = 0; $apr_m = 0; $may_m = 0; $jun_m = 0; $jul_m = 0; $aug_m = 0; $sep_m = 0; $oct_m = 0; $nov_m = 0; $dec_m = 0;
        $jan_a = 0; $feb_a = 0; $mar_a = 0; $apr_a = 0; $may_a = 0; $jun_a = 0; $jul_a = 0; $aug_a = 0; $sep_a = 0; $oct_a = 0; $nov_a = 0; $dec_a = 0;

        $em = $this->getDoctrine()->getManager();
        $matchType = $em->getRepository('AppBundle:BookingType')->find(BookingType::MATCH);
        $subscriptionType = $em->getRepository('AppBundle:BookingType')->find(BookingType::ABONNEMENT);

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

        foreach ( $matchs as $value) {
            if ($value->getDate()->format('Y-m') == date('Y-01')) { $jan_m = $jan_m + 1; }
            if ($value->getDate()->format('Y-m') == date('Y-02')) { $feb_m = $feb_m + 1; }
            if ($value->getDate()->format('Y-m') == date('Y-03')) { $mar_m = $mar_m + 1; }
            if ($value->getDate()->format('Y-m') == date('Y-04')) { $apr_m = $apr_m + 1; }
            if ($value->getDate()->format('Y-m') == date('Y-05')) { $may_m = $may_m + 1; }
            if ($value->getDate()->format('Y-m') == date('Y-06')) { $jun_m = $jun_m + 1; }
            if ($value->getDate()->format('Y-m') == date('Y-07')) { $jul_m = $jul_m + 1; }
            if ($value->getDate()->format('Y-m') == date('Y-08')) { $aug_m = $aug_m + 1; }
            if ($value->getDate()->format('Y-m') == date('Y-09')) { $sep_m = $sep_m + 1; }
            if ($value->getDate()->format('Y-m') == date('Y-10')) { $oct_m = $oct_m + 1; }
            if ($value->getDate()->format('Y-m') == date('Y-11')) { $nov_m = $nov_m + 1; }
            if ($value->getDate()->format('Y-m') == date('Y-12')) { $dec_m = $dec_m + 1; }
        }

        foreach ( $subscriptions as $value) {
            if ($value->getDate()->format('Y-m') == date('Y-01')) { $jan_a = $jan_a + 1; }
            if ($value->getDate()->format('Y-m') == date('Y-02')) { $feb_a = $feb_a + 1; }
            if ($value->getDate()->format('Y-m') == date('Y-03')) { $mar_a = $mar_a + 1; }
            if ($value->getDate()->format('Y-m') == date('Y-04')) { $apr_a = $apr_a + 1; }
            if ($value->getDate()->format('Y-m') == date('Y-05')) { $may_a = $may_a + 1; }
            if ($value->getDate()->format('Y-m') == date('Y-06')) { $jun_a = $jun_a + 1; }
            if ($value->getDate()->format('Y-m') == date('Y-07')) { $jul_a = $jul_a + 1; }
            if ($value->getDate()->format('Y-m') == date('Y-08')) { $aug_a = $aug_a + 1; }
            if ($value->getDate()->format('Y-m') == date('Y-09')) { $sep_a = $sep_a + 1; }
            if ($value->getDate()->format('Y-m') == date('Y-10')) { $oct_a = $oct_a + 1; }
            if ($value->getDate()->format('Y-m') == date('Y-11')) { $nov_a = $nov_a + 1; }
            if ($value->getDate()->format('Y-m') == date('Y-12')) { $dec_a = $dec_a + 1; }
        }

        $payload=array();
        $payload['status']='ok';
        $payload['page']='show';
        $payload['match'] = [$jan_m, $feb_m, $mar_m, $apr_m, $may_m, $jun_m, $jul_m, $aug_m, $sep_m, $oct_m, $nov_m, $dec_m];
        $payload['abonnement'] = [$jan_a, $feb_a, $mar_a, $apr_a, $may_a, $jun_a, $jul_a, $aug_a, $sep_a, $oct_a, $nov_a, $dec_a];

        return new Response(json_encode($payload));

    }

    /**
     * Calculated nb total sessions of current user
     * @Route("/session/chart", name="session_show")
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN') or has_role('ROLE_USER')")
     * @Method("GET")
     * @return Response
     */
    public function getSessionMonthly()
    {
        $jan = 0; $feb = 0; $mar = 0; $apr = 0; $may = 0; $jun = 0; $jul = 0; $aug = 0; $sep = 0; $oct = 0; $nov = 0; $dec = 0;
        $jan_later = 0; $feb_later = 0; $mar_later = 0; $apr_later = 0; $may_later = 0; $jun_later = 0; $jul_later = 0; $aug_later = 0; $sep_later = 0; $oct_later = 0; $nov_later = 0; $dec_later = 0;
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
            if ($value->getCreatedAt()->format('Y-m') == date('Y-01')) { $jan = $jan + 1;
            } elseif ($value->getCreatedAt()->format('Y-m') == date('Y-01', strtotime('-1 years'))) { $jan_later = $jan_later + 1; }
            if ($value->getCreatedAt()->format('Y-m') == date('Y-02')) { $feb = $feb + 1;
            } elseif ($value->getCreatedAt()->format('Y-m') == date('Y-02', strtotime('-1 years'))) { $feb_later = $feb_later + 1; }
            if ($value->getCreatedAt()->format('Y-m') == date('Y-03')) { $mar = $mar + 1;
            } elseif ($value->getCreatedAt()->format('Y-m') == date('Y-03', strtotime('-1 years'))) { $mar_later = $mar_later + 1; }
            if ($value->getCreatedAt()->format('Y-m') == date('Y-04')) { $apr = $apr + 1;
            } elseif ($value->getCreatedAt()->format('Y-m') == date('Y-04', strtotime('-1 years'))) { $apr_later = $apr_later + 1; }
            if ($value->getCreatedAt()->format('Y-m') == date('Y-05')) { $may = $may + 1;
            } elseif ($value->getCreatedAt()->format('Y-m') == date('Y-05', strtotime('-1 years'))) { $may_later = $may_later + 1; }
            if ($value->getCreatedAt()->format('Y-m') == date('Y-06')) { $jun = $jun + 1;
            } elseif ($value->getCreatedAt()->format('Y-m') == date('Y-06', strtotime('-1 years'))) { $jun_later = $jun_later + 1; }
            if ($value->getCreatedAt()->format('Y-m') == date('Y-07')) { $jul = $jul + 1;
            } elseif ($value->getCreatedAt()->format('Y-m') == date('Y-07', strtotime('-1 years'))) { $jul_later = $jul_later + 1; }
            if ($value->getCreatedAt()->format('Y-m') == date('Y-08')) { $aug = $aug + 1;
            } elseif ($value->getCreatedAt()->format('Y-m') == date('Y-08', strtotime('-1 years'))) { $aug_later = $aug_later + 1; }
            if ($value->getCreatedAt()->format('Y-m') == date('Y-09')) { $sep = $sep + 1;
            } elseif ($value->getCreatedAt()->format('Y-m') == date('Y-09', strtotime('-1 years'))) { $sep_later = $sep_later + 1; }
            if ($value->getCreatedAt()->format('Y-m') == date('Y-10')) { $oct = $oct + 1;
            } elseif ($value->getCreatedAt()->format('Y-m') == date('Y-10', strtotime('-1 years'))) { $oct_later = $oct_later + 1; }
            if ($value->getCreatedAt()->format('Y-m') == date('Y-11')) { $nov = $nov + 1;
            } elseif ($value->getCreatedAt()->format('Y-m') == date('Y-11', strtotime('-1 years'))) { $nov_later = $nov_later + 1; }
            if ($value->getCreatedAt()->format('Y-m') == date('Y-12')) { $dec = $dec + 1;
            } elseif ($value->getCreatedAt()->format('Y-m') == date('Y-12', strtotime('-1 years'))) { $dec_later = $dec_later + 1; }
        }

        $payload=array();
        $payload['status']='ok';
        $payload['page']='show';
        $payload['session'] = [$jan, $feb, $mar, $apr, $may, $jun, $jul, $aug, $sep, $oct, $nov, $dec];
        $payload['session_later'] = [$jan_later, $feb_later, $mar_later, $apr_later, $may_later, $jun_later, $jul_later, $aug_later, $sep_later, $oct_later, $nov_later, $dec_later];
        return new Response(json_encode($payload));
    }

    /**
     * Calculated nb total sessions of current user
     * @Route("/booking_type/chart", name="booking_type_show")
     * @Security("has_role('ROLE_SUPER_ADMIN' ) or has_role('ROLE_ADMIN') or has_role('ROLE_USER')")
     * @Method("GET")
     * @return Response
     */
    public function getTotalBookingType()
    {
        $em = $this->getDoctrine()->getManager();
        $matchType = $em->getRepository('AppBundle:BookingType')->find(BookingType::MATCH);
        $subscriptionType = $em->getRepository('AppBundle:BookingType')->find(BookingType::ABONNEMENT);

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

    /** ============================================================================================================== */
    /** === Form Builder search ====================================================================================== */
    /** ============================================================================================================== */

    /**
     * Get city by region for center.
     * @Route("/register/region/{id}", name="get_city_by_region")
     * @Method("GET")
     * @return Response
     */
    function getCityByRegionAction($id)
    {
        $city = null;
        $em = $this->getDoctrine()->getManager();
        $region = $em->getRepository('AppBundle:Region')->find($id);
        $city = $em->getRepository('AppBundle:City')->findBy(array('region' => $region));

        $payload=array();
        $payload['status']='ok';
        $payload['page']='show';
        $payload['html'] = $this->renderView('Default/form_city.html.twig', [
            'city' => $city,
        ]);

        return new Response(json_encode($payload));
    }


}
