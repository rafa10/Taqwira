<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Bill;
use AppBundle\Entity\Booking;
use AppBundle\Entity\Customer;
use AppBundle\Entity\Notification;
use AppBundle\Entity\Session;
use AppBundle\Form\CustomerType;
use AppBundle\Form\BookingType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * Booking Controller
 *
 * Class BookingController
 * @Route("/booking")
 * @package AppBundle\Controller
 */
class BookingController extends Controller
{
    /**
     * constant
     */
    const MESSAGE = 'Message';
    const BOOKING = 'Booking';
    /** ============================================================================================================== */
    /** =========================================== Booking Match ==================================================== */
    /** ============================================================================================================== */
    /**
     * Lists all booking match entities.
     * @Route("/match", name="booking_match_index")
     * @Method("GET")
     */
    public function indexMatchAction()
    {
        $em = $this->getDoctrine()->getManager();

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $type = $em->getRepository('AppBundle:BookingType')->findBy(array('description' => 'Match'));
            $bookings = $em->getRepository('AppBundle:Booking')->findBy(array('bookingType' => $type));

        } else {
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $center = $userLogin->getCenter();
            $fields = $em->getRepository('AppBundle:Field')->findBy(array('center' => $center));
            $type = $em->getRepository('AppBundle:BookingType')->findBy(array('description' => 'Match'));
            $bookings = $em->getRepository('AppBundle:Booking')->findBy(array('field' => $fields, 'bookingType' => $type));
        }

        return $this->render('booking/match/index_match.html.twig', array(
            'bookings' => $bookings
        ));
    }

    /**
     * Lists all match available by date .
     * @Route("/match/new", name="booking_match_new")
     * @Method({"GET", "POST"})
     */
    public function newMatchAction()
    {
        $em = $this->getDoctrine()->getManager();
        $userLogin = $this->get('security.token_storage')->getToken()->getUser();
        $center = $userLogin->getCenter();
        $fields = $em->getRepository('AppBundle:Field')->findBy(array('center' => $center));

        $form = $this->formBuilderSearch($center);

        return $this->render('booking/match/new_match.html.twig', array(
            'form' => $form->createView(),
            'fields' => $fields
        ));
    }

    /**
     * Search match date .
     * @Route("/match/search", name="search_match")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function searchMatchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $userLogin = $this->get('security.token_storage')->getToken()->getUser();
        $center = $userLogin->getCenter();
        $days = $em->getRepository('AppBundle:Day')->findAll();

        $data = $request->request->get('form');
        $fieldID = isset($data['field']) ? $data['field'] : null;
        $date = isset($data['date']) ? $data['date'] : null;
        $date_search = new \DateTime($date);

        if ($fieldID == null){
            $fields = $em->getRepository('AppBundle:Field')->findBy(array('center' => $center));

            $bookingsTab = [];
            $bookings = [];
            foreach ( $fields as $field ){
                $bookingsTab[] = $em->getRepository('AppBundle:Booking')->findBy(array('field' => $field));
            }
            foreach ( $bookingsTab as $index ){
                foreach ($index as $item){
                    $bookings[] = $item;
                }
            }

        } else {
            $fields = $em->getRepository('AppBundle:Field')->find($fieldID);
            $bookings = $em->getRepository('AppBundle:Booking')->findBy(array('field' => $fields ));

        }

        $payload=array();
        $payload['status']='ok';
        $payload['page']='search';
        $payload['html'] = $this->renderView('booking/match/search_match.html.twig', array(
            'fields' => $fields,
            'date_search' => $date_search,
            'days' => $days,
            'bookings' => $bookings
        ));

        return new Response(json_encode($payload));

    }

    /**
     * Creates a new booking match entity.
     * @Route("/match/details", name="booking_match_details")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function bookingMatchAction(Request $request )
    {
        $em = $this->getDoctrine()->getManager();
        $booking = new Booking();

        if ($request->get('field') && $request->get('date') && $request->get('timeS') && $request->get('timeE') && $request->get('price')) {
            $fieldId = $request->get('field');
            $field = $em->getRepository('AppBundle:Field')->find($fieldId);
            $date = new \DateTime($request->get('date'));
            $timeStart = new \DateTime($request->get('timeS'));
            $timeEnd = new \DateTime($request->get('timeE'));
            $price = $request->get('price');

            // insert all form field booking
            $booking->setField($field);
            $booking->setDate($date);
            $booking->setTimeStart($timeStart);
            $booking->setTimeEnd($timeEnd);
            $booking->setPrice($price);
        }

        $reference  = $this->refHash(6);
        $booking->setReference($reference);
        $type = $em->getRepository('AppBundle:BookingType')->findOneBy(array('description' => 'Match'));
        $booking->setBookingType($type);

        $form = $this->createForm(BookingType::class, $booking, array(
            'action' => $this->generateUrl('booking_match_details')
        ));

        if (empty($this->isGranted('ROLE_SUPER_ADMIN'))){
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $center = $userLogin->getCenter();
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $request->request->all();
            $email = isset($data['booking']['customer']['email']) ? $data['booking']['customer']['email'] : null;
            $customer = $em->getRepository('AppBundle:Customer')->findOneBy(array('email' => $email, 'center' => $center));
            if(!empty($customer)){
                $booking->setCustomer($customer);
            }

            $em->persist($booking);
            $em->flush();

            // Send mail confirmation booking to customer
            $this->container->get('app.mailer')->sendBookingMessage($booking);

            // Create notification for dashboard admin center
            $this->container->get('app.notification')->newNotification($center, $subject=Notification::BOOKING, $link=Notification::BOOKING_LINK);

//            $request->getSession()
//                ->getFlashBag()
//                ->add('success', 'The booking successfully created!');

            $payload=array();
            $payload['status']='ok';
            $payload['page']='refresh';
            return new Response(json_encode($payload));

        }

        $payload=array();
        $payload['status']='ok';
        $payload['page']='new';
        $payload['html'] = $this->renderView('booking/match/booking_match_details.html.twig', array(
            'form' => $form->createView(),
            'field_name' => $field->getName()
        ));

        return new Response(json_encode($payload));
    }

    /**
     * Deletes a Center entity.
     * @Route("/{id}/delete", name="booking_match_delete")
     * @Method("GET")
     * @param Booking $booking
     * @return Response
     */
    public function deleteMatchAction(Request $request, Booking $booking)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($booking);
        $em->flush();

//        $request->getSession()
//            ->getFlashBag()
//            ->add('success', 'The booking match successfully deleted!');

        $payload=array();
        $payload['status']='ok';
        $payload['page']='refresh';
        return new Response(json_encode($payload));

    }

    /** ============================================================================================================== */
    /** ===================================== Booking subscription =================================================== */
    /** ============================================================================================================== */
    /**
     * Lists all booking subscription entities.
     * @Route("/subscription", name="booking_subscription_index")
     * @Method("GET")
     */
    public function indexSubscriptionAction()
    {
        $em = $this->getDoctrine()->getManager();

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $type = $em->getRepository('AppBundle:BookingType')->findBy(array('description' => 'Abonnement'));
            $bookings = $em->getRepository('AppBundle:Booking')->findBy(array('bookingType' => $type));
        } else {
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $center = $userLogin->getCenter();
            $fields = $em->getRepository('AppBundle:Field')->findBy(array('center' => $center));
            $type = $em->getRepository('AppBundle:BookingType')->findBy(array('description' => 'Abonnement'));
            $bookings = $em->getRepository('AppBundle:Booking')->findBy(array('field' => $fields, 'bookingType' => $type));
        }

        return $this->render('booking/subscription/index_subscription.html.twig', array(
            'bookings' => $bookings
        ));
    }

    /**
     * Lists all subscription available by date .
     * @Route("/subscription/new", name="booking_subscription_new")
     * @Method({"GET", "POST"})
     */
    public function newSubscriptionAction()
    {
        $em = $this->getDoctrine()->getManager();
        $userLogin = $this->get('security.token_storage')->getToken()->getUser();
        $center = $userLogin->getCenter();
        $fields = $em->getRepository('AppBundle:Field')->findBy(array('center' => $center));

        $form = $this->formBuilderSearch($center);

        return $this->render('booking/subscription/new_subscription.html.twig', array(
            'form' => $form->createView(),
            'fields' => $fields
        ));
    }

    /**
     * Lists all subscription available by date .
     * @Route("/subscription/search", name="search_subscription")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function searchSubscriptionAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $userLogin = $this->get('security.token_storage')->getToken()->getUser();
        $center = $userLogin->getCenter();
        $days = $em->getRepository('AppBundle:Day')->findAll();

        $data = $request->request->get('form');
        $fieldID = isset($data['field']) ? $data['field'] : null;
        $date = isset($data['date']) ? $data['date'] : null;
        $date_search = new \DateTime($date);

        if ($fieldID == null){
            $fields = $em->getRepository('AppBundle:Field')->findBy(array('center' => $center));

            $bookingsTab = [];
            $bookings = [];
            foreach ( $fields as $field ){
                $bookingsTab[] = $em->getRepository('AppBundle:Booking')->findBy(array('field' => $field));
            }
            foreach ( $bookingsTab as $index ){
                foreach ($index as $item){
                    $bookings[] = $item;
                }
            }

        } else {
            $fields = $em->getRepository('AppBundle:Field')->find($fieldID);
            $bookings = $em->getRepository('AppBundle:Booking')->findBy(array('field' => $fields ));

        }

        $payload=array();
        $payload['status']='ok';
        $payload['page']='search';
        $payload['html'] = $this->renderView('booking/subscription/search_subscription.html.twig', array(
            'fields' => $fields,
            'date_search' => $date_search,
            'days' => $days,
            'bookings' => $bookings
        ));

        return new Response(json_encode($payload));

    }

    /**
     * Creates a new booking match entity.
     * @Route("/subscription/basket/new", name="subscription_basket_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function newSubscriptionBasketAction(Request $request )
    {
        $em = $this->getDoctrine()->getManager();
        $booking = new Booking();
        $session = $request->getSession();

        if(!$session->has('basket')) $session->set('basket', array());
        $basket = $session->get('basket', array());


        if ($request->get('field') && $request->get('date') && $request->get('timeS') && $request->get('timeE') && $request->get('price')) {
            $fieldId = $request->get('field');
            $field = $em->getRepository('AppBundle:Field')->find($fieldId);
            $date = new \DateTime($request->get('date'));
            $timeStart = new \DateTime($request->get('timeS'));
            $timeEnd = new \DateTime($request->get('timeE'));
            $price = $request->get('price');
            $reference  = $this->refHash(6);
            $type = $em->getRepository('AppBundle:BookingType')->findOneBy(array('description' => 'Abonnement'));

            // insert all form booking in session bookingBasket
            $booking->setReference($reference);
            $booking->setBookingType($type);
            $booking->setField($field);
            $booking->setDate($date);
            $booking->setTimeStart($timeStart);
            $booking->setTimeEnd($timeEnd);
            $booking->setPrice($price);

            array_push($basket, $booking);

        }

        $request->getSession()->set('basket', $basket);

        $payload=array();
        $payload['status']='ok';
        $payload['page']='refresh';
        return new Response(json_encode($payload));
    }

    /**
     * Lists all basket subscription entities.
     * @Route("/subscription/basket/show", name="subscription_basket_show")
     * @Method("GET")
     */
    public function showSubscriptionBasketAction()
    {
        return $this->render('booking/subscription/show_subscription_basket.html.twig');
    }

    /**
     * Creates a new booking subscription entity.
     * @Route("/subscription/details", name="booking_subscription_details")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function bookingSubscriptionAction(Request $request )
    {

        $em = $this->getDoctrine()->getManager();
        $customer = new Customer();

        $form = $this->createForm(CustomerType::class, $customer, array(
            'action' => $this->generateUrl('booking_subscription_details')
        ));

        if (empty($this->isGranted('ROLE_SUPER_ADMIN'))){
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $center = $userLogin->getCenter();
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $request->request->all();
            $email = isset($data['customer']['email']) ? $data['customer']['email'] : null;
            $customerExist = $em->getRepository('AppBundle:Customer')->findOneBy(array('email' => $email, 'center' => $center));

            if(!empty($customerExist)){
                $customer = $customerExist;

            } else {
                $em->persist($customer);
                $em->flush();
            }

            $session = $request->getSession();
            $basket = $session->get('basket');

            foreach ($basket as $data){
                $type = $em->getRepository('AppBundle:BookingType')->find($data->getBookingType()->getId());
                $field = $em->getRepository('AppBundle:Field')->find($data->getField()->getId());

                $booking = new Booking();
                $booking->setReference($data->getReference());
                $booking->setBookingType($type);
                $booking->setField($field);
                $booking->setDate($data->getDate());
                $booking->setTimeStart($data->getTimeStart());
                $booking->setTimeEnd($data->getTimeEnd());
                $booking->setPrice($data->getPrice());
                $booking->setCustomer($customer);
                $em->persist($booking);
                $em->flush();
            }

            // Send mail confirmation booking to customer
            $this->container->get('app.mailer')->sendBookingSubscriptionMessage($basket, $customer);

            $session->remove('basket');

//            $request->getSession()
//                ->getFlashBag()
//                ->add('success', 'The booking subscription successfully created!');

            $payload=array();
            $payload['status']='ok';
            $payload['page']='refresh';
            return new Response(json_encode($payload));

        }

        $payload=array();
        $payload['status']='ok';
        $payload['page']='new';
        $payload['html'] = $this->renderView('booking/subscription/booking_subscription_details.html.twig', array(
            'form' => $form->createView(),
        ));

        return new Response(json_encode($payload));
    }

    /**
     * Deletes a Center entity.
     * @Route("/{id}/delete", name="booking_subscription_delete")
     * @Method("GET")
     * @param Booking $booking
     * @return Response
     */
    public function deleteSubscriptionAction(Request $request, Booking $booking)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($booking);
        $em->flush();

//        $request->getSession()
//            ->getFlashBag()
//            ->add('success', 'The booking subscription successfully deleted!');

        $payload=array();
        $payload['status']='ok';
        $payload['page']='refresh';
        return new Response(json_encode($payload));

    }

    /**
     * Deletes a Center entity.
     * @Route("/subscription/basket/{id}/delete", name="subscription_basket_delete")
     * @Method("GET")
     * @param $id
     * @return Response
     */
    public function deleteSubscriptionBasketAction(Request $request, $id)
    {
        $session = $request->getSession();
        $basket = $session->get('basket');

        foreach ($basket as $index => $data){
            if($data->getReference() == $id ){
                unset($basket[$index]);
                $session->set('basket', $basket);
            }
        }

//        $request->getSession()
//            ->getFlashBag()
//            ->add('success', 'The basket subscription successfully deleted!');

        $payload=array();
        $payload['status']='ok';
        $payload['page']='refresh';
        return new Response(json_encode($payload));

    }

    /** ============================================================================================================== */
    /** ====================================== /End subscription ===================================================== */
    /** ============================================================================================================== */

    /**
     * form builder search
     * @param $center
     * @return mixed
     */
    public function formBuilderSearch($center)
    {
        $form = $this->createFormBuilder()
            ->add('field', EntityType::class, array(
                'class' => 'AppBundle:Field',
                'query_builder' => function (EntityRepository $er) use ($center){
                    return $er->createQueryBuilder('f')
                        ->where('f.center = :center')
                        ->setParameter('center', $center->getId());
                },
                'required'    => false,
                'placeholder' => 'Choisissez ...',
                'empty_data'  => null

            ))
            ->add('date', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'required' => true,
                'attr' => array(
                    'class' => 'datepicker',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'dd-mm-yyyy'
                )
            ))
            ->getForm();

        return $form;

    }

    /**
     * @param $qtd
     * @return null|string
     */
    public function refHash($qtd)
    {
        //Under the string $Caracteres you write all the characters you want to be used to randomly generate the code.
        $Caracteres = 'ABCDEFGHIJKLMOPQRSTUVXWYZ0123456789';
        $QuantidadeCaracteres = strlen($Caracteres);
        $QuantidadeCaracteres--;

        $hash = null;
        for ($x=1; $x<=$qtd; $x++){
            $postion = rand(0, $QuantidadeCaracteres);
            $hash .=substr($Caracteres, $postion,1);
        }
        return $hash;

    }

    /**
     * form builder customer
     * @param $form
     * @param $center
     * @return mixed
     */
    public function formBuilderCustomer($form, $center)
    {
        $form
            ->add('customer', EntityType::class, array(
                'class' => 'AppBundle:Customer',
                'query_builder' => function (EntityRepository $er) use ($center){
                    return $er->createQueryBuilder('c')
                        ->where('c.center = :center')
                        ->setParameter('center', $center->getId());
                },
                'required'    => false,
                'placeholder' => 'Choisissez ...',
                'empty_data'  => null

            ))
        ;
        return $form;
    }

}
