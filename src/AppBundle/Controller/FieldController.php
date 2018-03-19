<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Field;
use AppBundle\Entity\User;
use AppBundle\Form\FieldType;
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
 * Center Controller
 * Class CompanyController
 * @Route("/field")
 * @package AppBundle\Controller
 */
class FieldController extends Controller
{

    /**
     * Lists all Center entities.
     * @Route("/", name="field_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        // Tous les centers uniquement pour les role_super_administrateur
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $fields = $em->getRepository('AppBundle:Field')->findAll();

        } else {
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $center = $userLogin->getCenter();
            $fields = $em->getRepository('AppBundle:Field')->findBy(array('center' => $center));

        }

        return $this->render('field/index.html.twig', array(
            'fields' => $fields
        ));
    }

    /**
     * Lists all Center entities.
     * @Route("/{id}/show", name="field_show")
     * @Method("GET")
     * @param Field $field
     * @return Response
     */
    public function showAction(Field $field)
    {
        $sessions = $field->getSession();

        $payload=array();
        $payload['status']='ok';
        $payload['page']='show';
        $payload['html'] = $this->renderView('field/show.html.twig', array(
            'field' => $field,
            'sessions' => $sessions
        ));

        return new Response(json_encode($payload));
    }


    /**
     * Creates a new Center entity.
     * @Route("/new", name="field_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $field = new Field();

        $form = $this->createForm(FieldType::class, $field, array(
            'action' => $this->generateUrl('field_new')
        ));

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $sessions = $em->createQuery('SELECT s FROM AppBundle:Session s ORDER BY s.id ASC')->getResult();

        } else {
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $center = $userLogin->getCenter();
            $sessions = $em->getRepository('AppBundle:Session')->findBy(array('center' => $center));

        }

        $form = $this->buildFormSessions($form, $sessions);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $request->request->all();
            // Insertion selection sessions
            $sessionsSelected = isset($data['field']['session']) ? $data['field']['session'] : [];
            // add all selected session
            foreach ($sessionsSelected as $value) {
                $session = $em->getRepository('AppBundle:Session')->find($value);
                $session->addField($field);
                $em->persist($session);
            }

            $em->persist($field);
            $em->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'The center successfully created!');

            $payload=array();
            $payload['status']='ok';
            $payload['page']='refresh';
            return new Response(json_encode($payload));

        }

        $payload=array();
        $payload['status']='ok';
        $payload['page']='new';
        $payload['html'] = $this->renderView('field/new.html.twig', array(
            'form' => $form->createView(),
        ));

        return new Response(json_encode($payload));
    }

    /**
     * Displays a form to edit an existing Center entity.
     * @Route("/{id}/edit", name="field_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Field $field
     *
     * @return Response
     */
    public function editAction(Request $request, Field $field)
    {
        if (null === $this->getUser()) {
            throw $this->createAccessDeniedException(User::USER_IS_NOT_LOGGED_IN);
        }

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(FieldType::class, $field, array(
            'action' => $this->generateUrl('field_edit',array('id'=>$field->getId()))
        ));

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $sessions = $em->createQuery('SELECT s FROM AppBundle:Session s ORDER BY s.id ASC')->getResult();

        } else {
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $center = $userLogin->getCenter();
            $sessions = $em->getRepository('AppBundle:Session')->findBy(array('center' => $center));

        }

        $form = $this->buildFormSessions($form, $sessions);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $data = $request->request->all();
                //=== Update sessions of field ===//
                if (isset($data['field']['session'])){
                    $sessionsSelected = $data['field']['session'];

                    // Remove the old selected sessions
                    $sessions = $field->getSession();
                    foreach ($sessions as $session) {
                        $session->removeField($field);
                        $em->persist($session);
                    }
                    // Add the new selected sessions
                    foreach ($sessionsSelected as $value) {
                        $session = $em->getRepository('AppBundle:Session')->find($value);
                        $session->addField($field);
                        $em->persist($session);
                    }

                }

                $em->persist($field);
                $em->flush();

                $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'The center successfully updated!');

                $payload=array();
                $payload['status']='ok';
                $payload['page']='refresh';
                return new Response(json_encode($payload));

            }
        }

        $payload = [];
        $payload['status'] = 'ok';
        $payload['page'] = 'edit';
        $payload['html'] = $this->renderView('field/edit.html.twig', [
            'field' => $field,
            'form' => $form->createView()
        ]);

        return new Response(json_encode($payload));
    }

    /**
     * Deletes a Center entity.
     * @Route("/{id}/delete", name="field_delete")
     * @Method("GET")
     * @param Field $field
     * @return Response
     */
    public function deleteAction(Request $request, Field $field)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($field);
        $em->flush();

        $request->getSession()
            ->getFlashBag()
            ->add('success', 'The field successfully deleted!');

        $payload=array();
        $payload['status']='ok';
        $payload['page']='refresh';
        return new Response(json_encode($payload));

    }

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
                    'placeholder' => 'Scéance',
                    'empty_data' => null
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

}
