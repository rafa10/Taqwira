<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Center;
use AppBundle\Entity\Customer;
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
 * Mail Controller
 *
 * Class MailController
 * @Route("/mail")
 * @package AppBundle\Controller
 */
class MailController extends Controller
{

    /**
     * Display rendered booking mail
     * @Route("/booking/match/{reference}", name="booking_match_mail")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method("GET")
     * @return Response
     */
    public function renderMailMatchAction($reference)
    {
        $em = $this->getDoctrine()->getManager();
        $booking= $em->getRepository('AppBundle:Booking')->findOneBy(array('reference' => $reference));

        return $this->render('mail/booking_mail.html.twig', array('booking' => $booking));
    }

}
