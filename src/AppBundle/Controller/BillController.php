<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Bill;
use AppBundle\Entity\Booking;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;


/**
 *
 * Bill Controller
 *
 * Class BillController
 * @Route("/bill")
 * @package AppBundle\Controller
 */
class BillController extends Controller
{

    /**
     * Render a pdf bill
     * @Route("/{id}/show", name="bill_match_pdf")
     * @Method("GET")
     * @param Booking $booking
     * @return PdfResponse
     */
    public function pdfAction(Booking $booking)
    {
        $center = $booking->getBill()->getCenter();
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('center' => $center));
        $number = str_pad($booking->getId(), 10, "0", STR_PAD_LEFT);
        $html =  $this->renderView('bill/bill.html.twig', array(
            'booking' => $booking,
            'user' => $user
        ));

        return new PdfResponse(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            'FACTURE'.$number.'.pdf'
        );
    }

    /**
     * Create a new bill booking match paid.
     * @Route("/match/{id}/paid", name="bill_match_paid")
     * @Method({"GET"})
     * @param Booking $booking
     * @return Response
     */
    public function newBillMatchAction(Request $request, Booking $booking)
    {
        $em = $this->getDoctrine()->getManager();
        $field = $em->getRepository('AppBundle:Field')->find($booking->getField()->getId());
        $center = $field->getCenter();
        $number = str_pad($booking->getId(), 10, "0", STR_PAD_LEFT);
        $date = new \DateTime();
        // creation de facture
        $bill = new Bill();
        $bill->setCenter($center);
        $bill->setNumber($number);
        $bill->setCreated($date);
        $em->persist($bill);
        $em->flush();
        // Modifier le status de réservation
        $booking->setBill($bill);
        $em->persist($booking);
        $em->flush();

//        $request->getSession()
//            ->getFlashBag()
//            ->add('success', 'The booking of match  successfully paid!');

        $payload = [];
        $payload['status'] = 'ok';
        $payload['page'] = 'refresh';

        return new Response(json_encode($payload));

    }

    /**
     * PCreate a new bill booking subscription paid.
     * @Route("/subscription/paid", name="bill_subscription_paid")
     * @Method({"GET", "POST"})
     * @return Response
     */
    public function newBillSubscriptionAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $date = new \DateTime();

        $arrayID = $request->request->get('id');

        foreach ( $arrayID as $id ){
            $booking = $em->getRepository('AppBundle:Booking')->find($id);
            $field = $em->getRepository('AppBundle:Field')->find($booking->getField()->getId());
            $center = $field->getCenter();
            $number = str_pad($booking->getId(), 10, "0", STR_PAD_LEFT);
            // creation de facture
            $bill = new Bill();
            $bill->setCenter($center);
            $bill->setNumber($number);
            $bill->setCreated($date);
            $em->persist($bill);
            $em->flush();
            // Modifier le status de réservation
            $booking->setBill($bill);
            $em->persist($booking);
            $em->flush();
            $em->persist($booking);
        }

        $em->flush();

//        $request->getSession()
//            ->getFlashBag()
//            ->add('success', 'The booking of subscription  successfully paid!');

        $payload = [];
        $payload['status'] = 'ok';
        $payload['page'] = 'refresh';

        return new Response(json_encode($payload));

    }


}
