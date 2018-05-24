<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Session;
use AppBundle\Form\SessionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Response;

/**
 *
 * Session Controller
 *
 * Class SessionController
 * @Route("/session")
 * @package AppBundle\Controller
 */
class SessionController extends Controller
{

    /**
     * Lists all session entities.
     * @Route("/", name="session_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        // Tous les sessions uniquement pour les role_super_administrateur
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $sessions = $em->getRepository('AppBundle:Session')->findAll();

        } else {
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $center = $userLogin->getCenter();
            $sessions = $em->getRepository('AppBundle:Session')->findBy(array('center' => $center));

        }

        return $this->render('session/index.html.twig', array(
            'sessions' => $sessions
        ));
    }


    /**
     * Creates a new session entity.
     * @Route("/new", name="session_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = new Session();

        $form = $this->createForm(SessionType::class, $session, array(
            'action' => $this->generateUrl('session_new')
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($session);
            $em->flush();

//            $request->getSession()
//                ->getFlashBag()
//                ->add('success', 'The session successfully created!');

            $payload=array();
            $payload['status']='ok';
            $payload['page']='refresh';
            return new Response(json_encode($payload));

        } else {
            $validator = $this->get('validator');
            $errors = $validator->validate($session);

            if (count($errors) > 0) {
                $payload=array();
                $payload['status']='ok';
                $payload['page']='new';
                $payload['html'] = $this->renderView('session/new.html.twig', array(
                    'form' => $form->createView(),
                    'errors' => $errors,
                ));
                return new Response(json_encode($payload));
            }
        }

        $payload=array();
        $payload['status']='ok';
        $payload['page']='new';
        $payload['html'] = $this->renderView('session/new.html.twig', array(
            'form' => $form->createView(),
        ));

        return new Response(json_encode($payload));
    }


    /**
     * Creates a new all session entity.
     * @Route("/all/new", name="all_session_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function newAllAction(Request $request)
    {
//        $em = $this->getDoctrine()->getManager();
//
//        $form = $this->formBuilderSession();
//
//        $form->handleRequest($request);
//        if ($form->isSubmitted() && $form->isValid()) {
//
//            $data = $request->request->get('form');
//            $time_start = isset($data['time_start']) ? $data['time_start'] : null;
//            $time_end = isset($data['time_end']) ? $data['time_end'] : null;
//            $step = isset($data['step']) ? $data['step'] : null;
//            $center = isset($data['center']) ? $data['center'] : null;
//
//            $em->persist($session);
//            $em->flush();
//
//            $request->getSession()
//                ->getFlashBag()
//                ->add('success', 'The session successfully created!');
//
//            $payload=array();
//            $payload['status']='ok';
//            $payload['page']='refresh';
//            return new Response(json_encode($payload));
//
//        } else {
//            $validator = $this->get('validator');
//            $errors = $validator->validate($session);
//
//            if (count($errors) > 0) {
//                $payload=array();
//                $payload['status']='ok';
//                $payload['page']='new';
//                $payload['html'] = $this->renderView('session/new.html.twig', array(
//                    'form' => $form->createView(),
//                    'errors' => $errors,
//                ));
//                return new Response(json_encode($payload));
//            }
//        }
//
//        $payload=array();
//        $payload['status']='ok';
//        $payload['page']='new';
//        $payload['html'] = $this->renderView('session/new.html.twig', array(
//            'form' => $form->createView(),
//        ));
//
//        return new Response(json_encode($payload));
    }


    /**
     * Displays a form to edit an existing session entity.
     * @Route("/{id}/edit", name="session_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    public function editAction(Request $request, Session $session)
    {
//        if (null === $this->getUser()) {
//            throw $this->createAccessDeniedException(User::USER_IS_NOT_LOGGED_IN);
//        }
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(SessionType::class, $session, array(
            'action' => $this->generateUrl('session_edit',array('id'=>$session->getId()))
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $em->persist($session);
                $em->flush();

//                $request->getSession()
//                    ->getFlashBag()
//                    ->add('success', 'The session successfully updated!');

                $payload=array();
                $payload['status']='ok';
                $payload['page']='refresh';
                return new Response(json_encode($payload));

            }
        }

        $payload = [];
        $payload['status'] = 'ok';
        $payload['page'] = 'edit';
        $payload['html'] = $this->renderView('session/edit.html.twig', [
            'session' => $session,
            'form' => $form->createView()
        ]);

        return new Response(json_encode($payload));
    }


    /**
     * Deletes a session entity.
     * @Route("/{id}/delete", name="session_delete")
     * @Method("GET")
     * @param Session $session
     * @return Response
     */
    public function deleteAction(Request $request, Session $session)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($session);
        $em->flush();

//        $request->getSession()
//            ->getFlashBag()
//            ->add('success', 'The session successfully deleted!');

        $payload=array();
        $payload['status']='ok';
        $payload['page']='refresh';
        return new Response(json_encode($payload));

    }

    //==================================================================================================================
    //==================================================================================================================
    //==================================================================================================================
    /**
     * form builder new all session
     * @return mixed
     */
    public function formBuilderSession()
    {
        $form = $this->createFormBuilder()
            ->add('time_start', TimeType::class, array(
                'widget' => 'single_text',
                'attr' => array(
                    'class' => 'timepicker'
                )
            ))
            ->add('time_end', TimeType::class,  array(
                'widget' => 'single_text',
                'attr' => array(
                    'class' => 'timepicker'
                )
            ))
            ->add('step', ChoiceType::class, array(
                'placeholder' => 'Choisissez ...',
                'empty_data' => null,
                'choices' => array(
                    '1h00' => '1:00',
                    '1h10' => '1:10',
                    '1h20' => '1:20',
                    '1h30' => '1:30',
                    '1h40' => '1:40',
                    '1h50' => '1:50',
                    '2h00' => '2:00',
                )
            ))
            ->add('center', EntityType::class, array(
                'class' => 'AppBundle:Center',
                'choice_label' => 'name',
                'placeholder' => 'Choisissez ...',
                'empty_data' => null
            ))
            ->getForm();

        return $form;

    }

}
