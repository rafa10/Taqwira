<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Center;
use AppBundle\Entity\Planning;
use AppBundle\Entity\User;
use AppBundle\Form\CenterType;
use AppBundle\Form\PlanningType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Response;

/**
 *
 * Center Controller
 *
 * Class PlanningController
 * @Route("/planning")
 * @package AppBundle\Controller
 */
class PlanningController extends Controller
{

    /**
     * Lists all planning entities.
     *
     * @Route("/", name="planning_index")
     * @Method("GET")
     */
    public function indexCenterAction()
    {
        $em = $this->getDoctrine()->getManager();

        // Tous les centers uniquement pour les role_super_administrateur
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $plannings = $em->getRepository('AppBundle:Planning')->findAll();
            $fields = null;
            $arrayField = null;
        } else {
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $center = $userLogin->getCenter();
            $fields = $em->getRepository('AppBundle:Field')->findBy(array('center' => $center));
            $plannings = $em->getRepository('AppBundle:Planning')->findBy(array('field' => $fields));
            $arrayField = $this->listAllFields($center);
        }



        return $this->render('planning/index.html.twig', array(
            'plannings' => $plannings,
            'fields' => $fields,
            'arrayField' =>  $arrayField
        ));
    }


    /**
     * Creates a new planning entity.
     *
     * @Route("/new", name="planning_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $planning = new Planning();

        $form = $this->createForm(PlanningType::class, $planning, array(
            'action' => $this->generateUrl('planning_new')
        ));


        if (empty($this->isGranted('ROLE_SUPER_ADMIN'))) {
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $center = $userLogin->getCenter();
            $form = $this->buildFormFields($form, $center);

        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($planning);
            $em->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'The planning successfully created!');

            $payload=array();
            $payload['status']='ok';
            $payload['page']='refresh';
            return new Response(json_encode($payload));

        } else {
            $validator = $this->get('validator');
            $errors = $validator->validate($planning);

            if (count($errors) > 0) {
                $payload=array();
                $payload['status']='ok';
                $payload['page']='new';
                $payload['html'] = $this->renderView('planning/new.html.twig', array(
                    'form' => $form->createView(),
                    'errors' => $errors,
                ));
                return new Response(json_encode($payload));
            }
        }

        $payload=array();
        $payload['status']='ok';
        $payload['page']='new';
        $payload['html'] = $this->renderView('planning/new.html.twig', array(
            'form' => $form->createView(),
        ));

        return new Response(json_encode($payload));
    }

    /**
     * Displays a form to edit an existing planning entity.
     *
     * @Route("/{id}/edit", name="planning_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Planning $planning
     *
     * @return Response
     */
    public function editAction(Request $request, Planning $planning)
    {
        if (null === $this->getUser()) {
            throw $this->createAccessDeniedException(User::USER_IS_NOT_LOGGED_IN);
        }

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(PlanningType::class, $planning, array(
            'action' => $this->generateUrl('planning_edit',array('id'=>$planning->getId()))
        ));

        if (empty($this->isGranted('ROLE_SUPER_ADMIN'))) {
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $center = $userLogin->getCenter();
            $form = $this->buildEditFormFields($form, $center);

        }

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $em->persist($planning);
                $em->flush();

                $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'The planning successfully updated!');

                $payload=array();
                $payload['status']='ok';
                $payload['page']='refresh';
                return new Response(json_encode($payload));

            }
        }

        $payload = [];
        $payload['status'] = 'ok';
        $payload['page'] = 'edit';
        $payload['html'] = $this->renderView('planning/edit.html.twig', [
            'planning' => $planning,
            'form' => $form->createView()
        ]);

        return new Response(json_encode($payload));
    }


    /**
     * Deletes a Center entity.
     *
     * @Route("/{id}/delete", name="planning_delete")
     * @Method("GET")
     * @param Planning $planning
     * @return Response
     */
    public function deleteAction(Request $request, Planning $planning)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($planning);
        $em->flush();

        $request->getSession()
            ->getFlashBag()
            ->add('success', 'The planning successfully deleted!');

        $payload=array();
        $payload['status']='ok';
        $payload['page']='refresh';
        return new Response(json_encode($payload));

    }


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


    /**
     * Get all Fields if is not planning
     * @param $center
     * @return array
     */
    private function listAllFields($center)
    {
        $fieldNotPlanning = [];
        $fieldPlanning = [] ;
        $em = $this->getDoctrine()->getManager();
        $fields = $em->getRepository('AppBundle:Field')->findBy(array('center' => $center));
        $plannings = $em->getRepository('AppBundle:Planning')->findBy(array('field' => $fields));
        if ($fields != null) {
            foreach ($fields as $field) {
                if($plannings != null) {
                    foreach ($plannings as $planning){
                        if ($field->getId() == $planning->getField()->getId()){
                            $fieldName = $field->getName();
                            $fieldPlanning[$fieldName] = $field;
                        }
                        $fieldName = $field->getName();
                        $fieldNotPlanning[$fieldName] = $field;
                    }
                } else {
                    $fieldName = $field->getName();
                    $fieldNotPlanning[$fieldName] = $field;
                }
            }
        }

        return $fieldAll = array_diff($fieldNotPlanning, $fieldPlanning);
    }



    /**
     * Ajax method for time start and time end by field
     *
     * @Route("/time_by_field", name="planning_new_time")
     * @Method({"GET", "POST"})
     * @return string|JsonResponse
     */
    public function ajaxMethod(Request $request)
    {

        if($request->request->get('id')){
            $ajaxMethods = $this->get('AjaxMethods');
            $data = $ajaxMethods->selectedTimeByField($request->request->get('id'));
            // pour vérifier la présence d'une requete Ajax
            if ($request->isXmlHttpRequest()){
                    return new JsonResponse($data);
            }
        }

        return new Response('This is not ajax!', 400);
    }


}
