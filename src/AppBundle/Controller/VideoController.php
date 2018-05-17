<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Session;
use AppBundle\Entity\Video;
use AppBundle\Form\SessionType;
use AppBundle\Form\VideoType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Response;

/**
 *
 * Video Controller
 *
 * Class VideoController
 * @Route("/video")
 * @package AppBundle\Controller
 */
class VideoController extends Controller
{

    /**
     * Lists all video entities.
     *
     * @Route("/", name="video_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        // Tous les videos uniquement pour les role_super_administrateur
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $videos = $em->getRepository('AppBundle:Video')->findAll();

        } else {
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $center = $userLogin->getCenter();
            $fields = $em->getRepository('AppBundle:Field')->findBy(array('center' => $center));
            $bookings = $em->getRepository('AppBundle:Booking')->findBy(array('field' => $fields));
            $videos = $em->getRepository('AppBundle:Video')->findBy(array('booking' => $bookings));

        }

        return $this->render('video/index.html.twig', array(
            'videos' => $videos
        ));
    }


    /**
     * show video entities.
     *
     * @Route("/{id}/show", name="video_show")
     * @Method({"GET", "POST"})
     * @param Video $video
     * @return Response
     */
    public function showAction(Video $video)
    {
        $payload=array();
        $payload['status']='ok';
        $payload['page']='show';
        $payload['html'] = $this->renderView('video/show.html.twig', array(
            'video' => $video,
        ));

        return new Response(json_encode($payload));
    }

    /**
     * Creates a new video entity.
     * @Route("/new", name="video_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $video = new Video();

        $form = $this->createForm(VideoType::class, $video, array(
            'action' => $this->generateUrl('video_new')
        ));

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $center = $em->getRepository('AppBundle:Center')->findAll();

        } else {
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $center = $userLogin->getCenter();
        }

        $form = $this->buildFormBooking($form, $center);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($video);
            $em->flush();

            $payload=array();
            $payload['status']='ok';
            $payload['page']='refresh';
            return new Response(json_encode($payload));


        } else {
            $validator = $this->get('validator');
            $errors = $validator->validate($video);

            if (count($errors) > 0) {
                $payload=array();
                $payload['status']='ok';
                $payload['page']='new';
                $payload['html'] = $this->renderView('video/new.html.twig', array(
                    'form' => $form->createView(),
                    'errors' => $errors,
                ));
                return new Response(json_encode($payload));
            }
        }

        $payload=array();
        $payload['status']='ok';
        $payload['page']='new';
        $payload['html'] = $this->renderView('video/new.html.twig', array(
            'form' => $form->createView(),
        ));

        return new Response(json_encode($payload));
    }

    /**
     * Displays a form to edit an existing video entity.
     *
     * @Route("/{id}/edit", name="video_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Video $video
     * @return Response
     */
    public function editAction(Request $request, Video $video)
    {
        if (null === $this->getUser()) {
            throw $this->createAccessDeniedException(User::USER_IS_NOT_LOGGED_IN);
        }
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(VideoType::class, $video, array(
            'action' => $this->generateUrl('video_edit',array('id'=>$video->getId()))
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $em->persist($video);
                $em->flush();

                $payload=array();
                $payload['status']='ok';
                $payload['page']='refresh';
                return new Response(json_encode($payload));

            }
        }

        $payload = [];
        $payload['status'] = 'ok';
        $payload['page'] = 'edit';
        $payload['html'] = $this->renderView('video/edit.html.twig', [
            'video' => $video,
            'form' => $form->createView()
        ]);

        return new Response(json_encode($payload));
    }


    /**
     * Deletes a video entity.
     *
     * @Route("/{id}/delete", name="video_delete")
     * @Method("GET")
     * @param Video $video
     * @return Response
     */
    public function deleteAction(Request $request, Video $video)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($video);
        $em->flush();

//        $request->getSession()
//            ->getFlashBag()
//            ->add('success', 'The video successfully deleted!');

        $payload=array();
        $payload['status']='ok';
        $payload['page']='refresh';
        return new Response(json_encode($payload));

    }

    /**
     * form builder booking
     *
     * @param $form
     * @param $center
     * @return Form
     */
    public function buildFormBooking($form, $center)
    {
        /* @var Form $form */
        $listBookings = $this->listAllBookings($center);

        $form
            ->add('booking', ChoiceType::class, [
                    'choices' => $listBookings,
                    'placeholder' => 'Choisissez ...',
                    'empty_data' => null
                ]
            )
        ;

        return $form;
    }

    /**
     * list all booking in array
     *
     * @param $center
     * @return array
     */
    public function listAllBookings($center)
    {
        $em = $this->getDoctrine()->getManager();
        $fields = $em->getRepository('AppBundle:Field')->findBy(array('center' => $center));
        $bookings = $em->getRepository('AppBundle:Booking')->findBy(array('field' => $fields), array('id' => 'DESC'));
        $videos = $em->getRepository('AppBundle:Video')->findBy(array('booking' => $bookings));

        $bookingsAll = [];
        foreach ($bookings as $booking) {
            foreach ($videos as $video ) {
                if ($booking->getId() != $video->getBooking()->getId() ){
                    if ($booking->getBill() != null ){
                        $sessionByRefrence = $booking->getReference().' == '.$booking->getCustomer()->getEmail().' == '.$booking->getDate()->format('d/m/Y');
                        $bookingsAll[$sessionByRefrence] = $booking;
                    }
                }
            }
        }

        return $bookingsAll;
    }

}
