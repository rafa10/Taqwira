<?php

namespace FrontBundle\Controller;

use AppBundle\Entity\Booking;
use AppBundle\Entity\Center;
use AppBundle\Entity\Field;
use AppBundle\Entity\Notification;
use AppBundle\Form\BookingType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Nzo\UrlEncryptorBundle\Annotations\ParamDecryptor;
use Nzo\UrlEncryptorBundle\Annotations\ParamEncryptor;


/**
 * Booking Search Controller
 *
 * Class BookingSearchController
 * @Route("/booking")
 * @package AppBundle\Controller
 */
class BookingSearchController extends Controller
{

    /**
     * form booking search .
     * @Route("", name="booking_page")
     */
    public function searchFirstMatchAction()
    {

        $form = $this->formBuilderSearch();

        return $this->render('FrontBundle:Booking_search:search.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * Search match date .
     * @Route("/match-search-result", name="booking_search")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function searchMatchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $days = $em->getRepository('AppBundle:Day')->findAll();

        $data = $request->request->get('form');
        $centerName = isset($data['center']) ? $data['center'] : null;
        $date = isset($data['date']) ? $data['date'] : date("Y-m-d");
        $date_search = new \DateTime($date);

        $center = $em->getRepository('AppBundle:Center')->findOneBy(array('name' => $centerName));
        $fields = $em->getRepository('AppBundle:Field')->findBy(array('center' => $center));
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('center' => $center));
        $events = $em->getRepository('AppBundle:Event')->findBy(array('center' => $center), array('created' => 'DESC'));

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

        $payload=array();
        $payload['status']='ok';
        $payload['page']='search';
        $payload['html'] = $this->renderView('FrontBundle:Booking_search:content_result.html.twig', array(
            'fields' => $fields,
            'date_search' => $date_search,
            'days' => $days,
            'bookings' => $bookings,
            'user' => $user,
            'center' => $center,
            'events' => $events
        ));

        return new Response(json_encode($payload));

    }

    /**
     * Creates a new booking match entity.
     * @Route("/match-details/{field}/{date}/{timeS}/{timeE}/{price}/", name="booking_details")
     * @ParamDecryptor(params={"field", "date", "timeS", "timeE", "price"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function bookingMatchAction(Request $request, $field, $date, $timeS, $timeE, $price)
    {
        $em = $this->getDoctrine()->getManager();
        $type = $em->getRepository('AppBundle:BookingType')->findOneBy(array('description' => 'Match'));
        $reference  = $this->refHash(6);
        $fiel = $em->getRepository('AppBundle:Field')->find($field);
        $dateMatch = new \DateTime($date);
        $timeStart = new \DateTime($timeS);
        $timeEnd = new \DateTime($timeE);
        $check_booking_match = $em->getRepository('AppBundle:Booking')->findBy(array('bookingType' => $type, 'date' => $dateMatch, 'timeStart' => $timeStart));
        $center = $fiel->getCenter();

        $booking = new Booking();
        $booking->setField($fiel);
        $booking->setDate($dateMatch);
        $booking->setTimeStart($timeStart);
        $booking->setTimeEnd($timeEnd);
        $booking->setPrice($price);

        $form = $this->createForm(BookingType::class, $booking, array(
            'action' => $this->generateUrl('booking_details', array(
                'field'=>$this->get('nzo_url_encryptor')->encrypt($field),
                'date'=>$this->get('nzo_url_encryptor')->encrypt($date),
                'timeS'=>$this->get('nzo_url_encryptor')->encrypt($timeS),
                'timeE'=>$this->get('nzo_url_encryptor')->encrypt($timeE),
                'price'=>$this->get('nzo_url_encryptor')->encrypt($price)
            ))
        ));

        // vérification si la réservation est toujour disponible au non
        if (empty($check_booking_match) ){

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $data = $request->request->all();
                $email = isset($data['booking']['customer']['email']) ? $data['booking']['customer']['email'] : null;
                $customer = $em->getRepository('AppBundle:Customer')->findOneBy(array('email' => $email, 'center' => $center));

                if(!empty($customer)){
                    $booking->setCustomer($customer);
                }

                $booking->setReference($reference);
                $booking->setBookingType($type);

                $em->persist($booking);
                $em->flush();

                // Send mail confirmation booking to customer
                $this->container->get('app.mailer')->sendBookingMessage($booking);

                // Create notification for dashboard admin center
                $this->container->get('app.notification')->newNotification($center, $subject=Notification::BOOKING, $link=Notification::BOOKING_LINK);

                $request->getSession()
                    ->getFlashBag()
                    ->add('SUCCÈS', 'La réservation a été enregistré avec succès!');


                return $this->redirectToRoute('booking_page');

            }

        } else {
            $request->getSession()
                ->getFlashBag()
                ->add('ERREUR', 'La réservation non disponible!');

            return $this->redirectToRoute('booking_page');
        }

        return $this->render('FrontBundle:Booking_search:booking_details.html.twig', array(
            'form' => $form->createView(),
            'name' => $fiel->getName(),
            'center' => $center
        ));

    }

    /** ============================================================================================================== */
    /** == other function ============================================================================================ */
    /** ============================================================================================================== */

    /**
     * @Route("/form/search", name="booking_form_search")
     */
    public function getFormSearchAction()
    {

        $regions = [];
        $citys = [];
        $centers = [];
        $em = $this->getDoctrine()->getManager();

        $regionAll = $em->getRepository('AppBundle:Region')->findAll();
        foreach ($regionAll as $region){
            $regions[$region->getName()] = null;
        }
        $cityAll = $em->getRepository('AppBundle:City')->findAll();
        foreach ($cityAll as $city){
            $citys[$city->getName()] = null;
        }

        $centerAll = $em->getRepository('AppBundle:Center')->findBy(array('share_program' => true));
        foreach ($centerAll as $center){
            $centers[$center->getName()] = null;
        }

        $regionCity = array_merge($regions, $citys);

        $payload=array();
        $payload['status']='ok';
        $payload['page']='show';
        $payload['region_city'] = $regionCity;
        $payload['center'] = $centers;

        return new Response(json_encode($payload));
    }

    /**
     * form builder search
     * @return mixed
     */
    public function formBuilderSearch()
    {
        $form = $this->createFormBuilder()
            ->add('ville', SearchType::class, array(
                'required' => false,
                'attr' => array(
                    'class' => 'autocomplete-region',
                )
            ))
            ->add('center', SearchType::class, array(
                'attr' => array(
                    'class' => 'autocomplete-center',
                )
            ))
            ->add('date', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'required' => true,
                'model_timezone' => 'Europe/Paris',
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
     * Search center by region or city  .
     * @Route("/city_or_region/{city_region}", name="center_by_city_region")
     * @Method("GET")
     * @return Response
     */
    public function searchCenterByCityOrRegionAction($city_region)
    {
        $em = $this->getDoctrine()->getManager();

        if (!empty($city_region)){
            $region = $em->getRepository('AppBundle:Region')->findByName(array('city' => $city_region));
            $city = $em->getRepository('AppBundle:City')->findByName(array('city' => $city_region));
            if (!empty($region)){
                $centers = $em->getRepository('AppBundle:Center')->findBy(array('region' => $region, 'share_program' => true ));
            } else {
                $centers = $em->getRepository('AppBundle:Center')->findBy(array('city' => $city, 'share_program' => true ));
            }
        }

        $data_center = [];
        foreach ( $centers as $center ){
            $data_center[] = $center->getName();
        }

        $payload=array();
        $payload['status']='ok';
        $payload['page']='search';
        $payload['html'] = $this->renderView('FrontBundle:Booking_search:center_form.html.twig', array(
            'centers' => $data_center
        ));
        return new Response(json_encode($payload));

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
     * Auto-complete form field customer
     * @Route("form/{center}/customer", name="get_customer")
     * @Method("GET")
     */
    public function getCustomerAction(Center $center)
    {
        $customers = [];
        $em = $this->getDoctrine()->getManager();

        $customersAll = $em->getRepository('AppBundle:Customer')->findBy(array('center' => $center));
        foreach ($customersAll as $customer){
            $customers[$customer->getEmail()] = null;
        }

        $payload=array();
        $payload['status']='ok';
        $payload['page']='show';
        $payload['customers'] = $customers;

        return new Response(json_encode($payload));
    }

}
