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

        } else {
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $center = $userLogin->getCenter();
            $fields = $em->getRepository('AppBundle:Field')->findBy(array('center' => $center));
            $customers = $em->getRepository('AppBundle:Customer')->findBy(array('center' => $center));
            $match = $em->getRepository('AppBundle:BookingType')->findBy(array('description' => 'Match'));
            $matchs = $em->getRepository('AppBundle:Booking')->findBy(array('field' => $fields ,'bookingType' => $match));
            $subscription = $em->getRepository('AppBundle:BookingType')->findBy(array('description' => 'Abonnement'));
            $subscriptions = $em->getRepository('AppBundle:Booking')->findBy(array('field' => $fields,'bookingType' => $subscription));

        }

        return $this->render('default/index.html.twig', array(
            'fields' => $fields,
            'customers' => $customers,
            'matchs' => $matchs,
            'subscriptions' => $subscriptions,
//            'users' => $users,

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

}
