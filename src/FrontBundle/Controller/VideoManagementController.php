<?php

namespace FrontBundle\Controller;

use AppBundle\Entity\Booking;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * VideoManagement Controller
 *
 * Class VideoManagementController
 * @Route("/video/management")
 * @package FrontBundle\Controller
 */
class VideoManagementController extends Controller
{
    /**
     * forms login video
     * @Route("/login", name="video_login")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->formBuilderSearchByReferenceAndMail();
        $session = $request->getSession();
        if($session->has('video')) {$session->remove('video');}

        // Form builder search by reference and email ==================================================================
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $request->request->all();
            $reference = isset($data['form']['reference_one']) ? $data['form']['reference_one'] : null;
            $email = isset($data['form']['email']) ? $data['form']['email'] : null;
            $token = isset($data['form']['_token']) ? $data['form']['_token'] : null;

            $customer = $em->getRepository('AppBundle:Customer')->findOneBy(array('email' => $email));
            $booking= $em->getRepository('AppBundle:Booking')->findOneBy(array('reference' => $reference, 'customer' => $customer ));

            if ($booking != null){
                // Create session video
                if(!$session->has('video')) {$session->set('video', $booking);}

                $payload=array();
                $payload['status']='ok';
                $payload['page']='show';
                $payload['reference'] = $booking->getReference();
                return new Response(json_encode($payload));

            } else {
                // send error message
                $payload=array();
                $payload['status']='ok';
                $payload['page']='refresh';
                return new Response(json_encode($payload));

            }

        }

        return $this->render('FrontBundle:video_management:login_video.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * Display customer booking details
     * @Route("/{reference}", name="video")
     * @Method("GET")
     * @param Request $request
     * @return Response
     */
    public function videoAction(Request $request, $reference)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        if($session->has('video')) {
            if ($session->get('video')->getReference() == $reference){
                $booking= $em->getRepository('AppBundle:Booking')->findOneBy(array('reference' => $reference));
                $video= $em->getRepository('AppBundle:Video')->findOneBy(array('booking' => $booking ));
                return $this->render('FrontBundle:video_management:video.html.twig', array(
                    'booking' => $booking,
                    'video'   => $video
                    ));

            } else {
                return $this->redirectToRoute('video_login');
            }

        } else {
           return $this->redirectToRoute('video_login');

        }
    }

    /**
     * form builder video search by reference and mail
     * @return mixed
     */
    public function formBuilderSearchByReferenceAndMail()
    {
        $form = $this->createFormBuilder()
            ->add('reference_one', TextType::class, array('attr' => array('maxlength' => 8)))
            ->add('email', EmailType::class)
            ->getForm();

        return $form;

    }

}
