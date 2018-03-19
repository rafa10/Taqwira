<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Center;
use AppBundle\Entity\Field;
use AppBundle\Entity\User;
use AppBundle\Form\CenterType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Response;

/**
 *
 * Calendar Controller
 *
 * Class CalendarController
 * @Route("/calendar")
 * @package AppBundle\Controller
 */
class CalendarController extends Controller
{

    /**
     * Lists all Center entities.
     *
     * @Route("/", name="calendar_index")
     * @Method("GET")
     */
    public function indexCalenderAction()
    {
        $em = $this->getDoctrine()->getManager();
        $userLogin = $this->get('security.token_storage')->getToken()->getUser();
        $center = $userLogin->getCenter();
        $fields = $em->getRepository('AppBundle:Field')->findBy(array('center' => $center));

        return $this->render('calendar/index.html.twig', array(
            'fields' => $fields
        ));
    }

    /**
     * Lists all Center entities.
     * @Route("/{id}/show", name="calendar_show")
     * @Method("GET")
     */
    public function getCalenderAction(Field $field)
    {
        $em = $this->getDoctrine()->getManager();

        // Tous les utilisateur uniquement pour les role_super_administrateur
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            // $fields = $em->getRepository('AppBundle:Field')->findAll();

        } else {
            $bookings = $em->getRepository('AppBundle:Booking')->findBy(array('field' => $field));

            $events = [];
            foreach ( $bookings as $booking){
                if($booking->getBookingType()->getDescription() == 'Match'){
                    $events[] = [
                        'title' => $booking->getBookingType()->getDescription(),
                        'start'=> $booking->getDate()->format('Y-m-d').'T'.$booking->getTimeStart()->format('H:i'),
                        'end'=> $booking->getDate()->format('Y-m-d').'T'.$booking->getTimeEnd()->format('H:i'),
                        'color'=> '#e91e63'
                    ];
                } elseif ($booking->getBookingType()->getDescription() == 'Abonnement') {
                    $events[] = [
                        'title' => $booking->getBookingType()->getDescription(),
                        'start'=> $booking->getDate()->format('Y-m-d').'T'.$booking->getTimeStart()->format('H:i'),
                        'end'=> $booking->getDate()->format('Y-m-d').'T'.$booking->getTimeEnd()->format('H:i'),
                        'color'=> '#009688'
                    ];
                } else {
                    $events[] = [
                        'title' => $booking->getBookingType()->getDescription(),
                        'start'=> $booking->getDate()->format('Y-m-d').'T'.$booking->getTimeStart()->format('H:i'),
                        'end'=> $booking->getDate()->format('Y-m-d').'T'.$booking->getTimeEnd()->format('H:i'),
                        'color'=> '#2196f3 '
                    ];
                }
            }

        }

        $payload=array();
        $payload['status']='ok';
        $payload['page']='show';
        $payload['booking']=$events;

        return new Response(json_encode($payload));
    }

}
