<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Program;
use AppBundle\Entity\User;
use AppBundle\Form\ProgramType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
 * Program Controller
 *
 * Class ProgramController
 * @Route("/academy/program")
 * @package AppBundle\Controller
 */
class ProgramController extends Controller
{

    /**
     * Lists all Center entities.
     *
     * @Route("/", name="program_index")
     * @Method("GET")
     */
    public function indexCenterAction()
    {
        $em = $this->getDoctrine()->getManager();

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $programs = $em->getRepository('AppBundle:Program')->findAll();
            $arrayField = null;

        } else {
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $center = $userLogin->getCenter();
            $fields = $em->getRepository('AppBundle:Field')->findBy(array('center' => $center));
            $programs = $em->getRepository('AppBundle:Program')->findBy(array('field' => $fields));
            $arrayField = $this->listAllFields($center);
        }

        return $this->render('program/index.html.twig', array(
            'programs' => $programs,
            'arrayField' => $arrayField
        ));
    }


    /**
     * Creates a new program entity.
     *
     * @Route("/new", name="program_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $program = new Program();

        $type = $em->getRepository('AppBundle:BookingType')->findOneBy(array('description' => "Académie"));
        $program->setBookingType($type);

        $form = $this->createForm(ProgramType::class, $program, array(
            'action' => $this->generateUrl('program_new')
        ));

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $sessions = $em->createQuery('SELECT s FROM AppBundle:Session s ORDER BY s.id ASC')->getResult();

        } else {
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $center = $userLogin->getCenter();
            $sessions = $em->getRepository('AppBundle:Session')->findBy(array('center' => $center));
            $form = $this->buildFormFields($form, $center);
        }

        $form = $this->buildFormSessions($form, $sessions);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $request->request->all();

            // Insertion selection days
            $daysSelected = isset($data['program']['day']) ? $data['program']['day'] : [];
            // add all selected session
            foreach ($daysSelected as $value) {
                $day = $em->getRepository('AppBundle:Day')->find($value);
                $day->addprogram($program);
                $em->persist($day);
            }

            // Insertion selection sessions
            $sessionsSelected = isset($data['program']['session']) ? $data['program']['session'] : [];
            // add all selected session
            foreach ($sessionsSelected as $value) {
                $session = $em->getRepository('AppBundle:Session')->find($value);
                $session->addprogram($program);
                $em->persist($session);
            }

            $em->persist($program);
            $em->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'The program successfully created!');

            $payload=array();
            $payload['status']='ok';
            $payload['page']='refresh';
            return new Response(json_encode($payload));

        } else {
            $validator = $this->get('validator');
            $errors = $validator->validate($program);

            if (count($errors) > 0) {
                $payload=array();
                $payload['status']='ok';
                $payload['page']='new';
                $payload['html'] = $this->renderView('program/new.html.twig', array(
                    'form' => $form->createView(),
                    'errors' => $errors,
                ));
                return new Response(json_encode($payload));
            }
        }

        $payload=array();
        $payload['status']='ok';
        $payload['page']='new';
        $payload['html'] = $this->renderView('program/new.html.twig', array(
            'form' => $form->createView(),
        ));

        return new Response(json_encode($payload));
    }

    /**
     * Displays a form to edit an existing program entity.
     *
     * @Route("/{id}/edit", name="program_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Program $program
     *
     * @return Response
     */
    public function editAction(Request $request, Program $program)
    {
        if (null === $this->getUser()) {
            throw $this->createAccessDeniedException(User::USER_IS_NOT_LOGGED_IN);
        }

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ProgramType::class, $program, array(
            'action' => $this->generateUrl('program_edit',array('id' => $program->getId()))
        ));

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $sessions = $em->createQuery('SELECT s FROM AppBundle:Session s ORDER BY s.id ASC')->getResult();

        } else {
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $center = $userLogin->getCenter();
            $sessions = $em->getRepository('AppBundle:Session')->findBy(array('center' => $center));
            $form = $this->buildEditFormFields($form, $center);
        }

        $form = $this->buildFormSessions($form, $sessions);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $data = $request->request->all();
                //=== update days of program ===//
                if (isset($data['program']['day'])){
                    $daysSelected = $data['program']['day'];

                    //Remove the old selected days
                    $days = $program->getDay();
                    foreach ($days as $day) {
                        $day->removeProgram($program);
                        $em->persist($day);
                    }

                    // add the selected days
                    foreach ($daysSelected as $value) {
                        $day = $em->getRepository('AppBundle:Day')->find($value);
                        $day->addProgram($program);
                        $em->persist($day);
                    }

                }
                //=== Update sessions of program ===//
                if (isset($data['program']['session'])){
                    $sessionsSelected = $data['program']['session'];

                    // Remove the old selected sessions
                    $sessions = $program->getSession();
                    foreach ($sessions as $session) {
                        $session->removeProgram($program);
                        $em->persist($session);
                    }
                    // Add the new selected sessions
                    foreach ($sessionsSelected as $value) {
                        $session = $em->getRepository('AppBundle:Session')->find($value);
                        $session->addProgram($program);
                        $em->persist($session);
                    }

                }

                $em->persist($program);
                $em->flush();

                $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'The program successfully updated!');

                $payload=array();
                $payload['status']='ok';
                $payload['page']='refresh';
                return new Response(json_encode($payload));

            }
        }

        $payload = [];
        $payload['status'] = 'ok';
        $payload['page'] = 'edit';
        $payload['html'] = $this->renderView('program/edit.html.twig', [
            'program' => $program,
            'form' => $form->createView()
        ]);

        return new Response(json_encode($payload));
    }


    /**
     * Deletes a program entity.
     *
     * @Route("/{id}/delete", name="program_delete")
     * @Method("GET")
     * @param Program $program
     * @return Response
     */
    public function deleteAction(Request $request, Program $program)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($program);
        $em->flush();

        $request->getSession()
            ->getFlashBag()
            ->add('success', 'The program successfully deleted!');

        $payload=array();
        $payload['status']='ok';
        $payload['page']='refresh';
        return new Response(json_encode($payload));

    }

//    ==================================================================================================================
//    === Other function ===============================================================================================
//    ==================================================================================================================

    /**
     * All Fields
     * @param $center
     * @return array
     */
    private function listAllFields($center)
    {
        $allField = [];
        $fieldProgram = [] ;
        $em = $this->getDoctrine()->getManager();
        $fields = $em->getRepository('AppBundle:Field')->findBy(array('center' => $center));
        $programs = $em->getRepository('AppBundle:Program')->findBy(array('field' => $fields));
        foreach ($programs as $program) {
            foreach ($fields as $field){
                if ($field->getId() == $program->getField()->getId()){
                    $fieldName = $field->getName();
                    $fieldProgram[$fieldName] = $field;
                }
                $fieldName = $field->getName();
                $allField[$fieldName] = $field;
            }
        }

        return $fieldAll = array_diff($allField, $fieldProgram);
    }

//    ==================================================================================================================
//    === Custom Form Builder ==========================================================================================
//    ==================================================================================================================

    /**
     * form builder session
     * @param $form
     * @param $sessions
     * @return Form
     */
    public function buildFormSessions($form, $sessions)
    {
        /* @var Form $form */
        $listSessions = $this->listAllSessions($sessions);

        $form
            ->add('session', ChoiceType::class, [
                    'choices' => $listSessions,
                    'mapped' => false,
                    'multiple' => true,
                ]
            )
        ;

        return $form;
    }

    /**
     * list all session in array
     * @param $sessions
     * @return array
     */
    public function listAllSessions($sessions)
    {
        $sessionsAll = [];
        foreach ($sessions as $session) {
            $sessionByTime = $session->getTimeStart()->format('H:i');
            $sessionsAll[$sessionByTime] = $session->getId();
        }

        return $sessionsAll;
    }

//    ==================================================================================================================

    /**
     * Form builder field by center
     *
     * @param $form
     * @param $center
     * @return \Symfony\Component\Form\Form
     */
    public function buildEditFormFields($form, $center)
    {
        /* @var \Symfony\Component\Form\Form $form */
        $form
            ->add('field', EntityType::class, array(
                'class' => 'AppBundle:Field',
                'query_builder' => function (EntityRepository $er) use ($center){
                    return $er->createQueryBuilder('f')
                        ->where('f.center = :center')
                        ->setParameter('center', $center->getId());
                }
            ))
        ;

        return $form;
    }


    /**
     * Build Form Fields
     * @param $form
     * @param $center
     * @return Form
     */
    private function buildFormFields($form, $center)
    {
        /* @var Form $form */
        $listFields = $this->listAllFields($center);

        $form
            ->add('field', ChoiceType::class, array(
                    'choices' => $listFields,
                    'placeholder' => 'Choisissez ...',
                    'empty_data' => null
                )
            );

        return $form;
    }





}
