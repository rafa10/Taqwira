<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Center;
use AppBundle\Entity\Day;
use AppBundle\Entity\Price;
use AppBundle\Entity\User;
use AppBundle\Form\CenterType;
use AppBundle\Form\PriceType;
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
 *
 * Class PriceController
 * @Route("/price")
 * @package AppBundle\Controller
 */
class PriceController extends Controller
{

    /**
     * Lists all Center entities.
     *
     * @Route("/", name="price_index")
     * @Method("GET")
     */
    public function indexCenterAction()
    {
        $em = $this->getDoctrine()->getManager();

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $prices = $em->getRepository('AppBundle:Price')->findAll();

        } else {
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $center = $userLogin->getCenter();
            $fields = $em->getRepository('AppBundle:Field')->findBy(array('center' => $center));
            $prices = $em->getRepository('AppBundle:Price')->findBy(array('field' => $fields));

        }

        return $this->render('price/index.html.twig', array(
            'prices' => $prices
        ));
    }


    /**
     * Creates a new price entity.
     *
     * @Route("/new", name="price_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        // list all days
        $days = $em->getRepository('AppBundle:Day')->findAll();
        $price = new Price();

        $form = $this->createForm(PriceType::class, $price, array(
            'action' => $this->generateUrl('price_new')
        ));

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $sessions = $em->createQuery('SELECT s FROM AppBundle:Session s ORDER BY s.id ASC')->getResult();

        } else {
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $center = $userLogin->getCenter();
            $sessions = $em->getRepository('AppBundle:Session')->findBy(array('center' => $center));
        }

        $form = $this->buildFormDays($form, $days);
        $form = $this->buildFormSessions($form, $sessions);
        $form = $this->buildFormFields($form, $center);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $request->request->all();

            // Insertion selection days
            $daysSelected = isset($data['price']['day']) ? $data['price']['day'] : [];
            // add all selected session
            foreach ($daysSelected as $value) {
                $day = $em->getRepository('AppBundle:Day')->find($value);
                $day->addPrice($price);
                $em->persist($day);
            }

            // Insertion selection sessions
            $sessionsSelected = isset($data['price']['session']) ? $data['price']['session'] : [];
            // add all selected session
            foreach ($sessionsSelected as $value) {
                $session = $em->getRepository('AppBundle:Session')->find($value);
                $session->addPrice($price);
                $em->persist($session);
            }

            $em->persist($price);
            $em->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'The price successfully created!');

            $payload=array();
            $payload['status']='ok';
            $payload['page']='refresh';
            return new Response(json_encode($payload));

        } else {
            $validator = $this->get('validator');
            $errors = $validator->validate($price);

            if (count($errors) > 0) {
                $payload=array();
                $payload['status']='ok';
                $payload['page']='new';
                $payload['html'] = $this->renderView('price/new.html.twig', array(
                    'form' => $form->createView(),
                    'errors' => $errors,
                ));
                return new Response(json_encode($payload));
            }
        }

        $payload=array();
        $payload['status']='ok';
        $payload['page']='new';
        $payload['html'] = $this->renderView('price/new.html.twig', array(
            'form' => $form->createView(),
        ));

        return new Response(json_encode($payload));
    }

    /**
     * Displays a form to edit an existing price entity.
     *
     * @Route("/{id}/edit", name="price_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Price $price
     * @return Response
     */
    public function editAction(Request $request, Price $price)
    {
        if (null === $this->getUser()) {
            throw $this->createAccessDeniedException(User::USER_IS_NOT_LOGGED_IN);
        }

        $em = $this->getDoctrine()->getManager();
        // list all days
        $days = $em->getRepository('AppBundle:Day')->findAll();

        $form = $this->createForm(PriceType::class, $price, array(
            'action' => $this->generateUrl('price_edit',array('id' => $price->getId()))
        ));

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $sessions = $em->createQuery('SELECT s FROM AppBundle:Session s ORDER BY s.id ASC')->getResult();

        } else {
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $center = $userLogin->getCenter();
            $sessions = $em->getRepository('AppBundle:Session')->findBy(array('center' => $center));
        }

        $form = $this->buildFormDays($form, $days);
        $form = $this->buildFormSessions($form, $sessions);
        $form = $this->buildFormFields($form, $center);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $data = $request->request->all();
                //=== update days of price ===========================================================================//
                if (isset($data['price']['day'])){
                    $daysSelected = $data['price']['day'];

                    //Remove the old selected days
                    $days = $price->getDay();
                    foreach ($days as $day) {
                        $day->removePrice($price);
                        $em->persist($day);
                    }

                    // add the selected days
                    foreach ($daysSelected as $value) {
                        $day = $em->getRepository('AppBundle:Day')->find($value);
                        $day->addPrice($price);
                        $em->persist($day);
                    }

                }
                //=== Update sessions of price =======================================================================//
                if (isset($data['price']['session'])){
                    $sessionsSelected = $data['price']['session'];

                    // Remove the old selected sessions
                    $sessions = $price->getSession();
                    foreach ($sessions as $session) {
                        $session->removePrice($price);
                        $em->persist($session);
                    }
                    // Add the new selected sessions
                    foreach ($sessionsSelected as $value) {
                        $session = $em->getRepository('AppBundle:Session')->find($value);
                        $session->addPrice($price);
                        $em->persist($session);
                    }

                }

                $em->persist($price);
                $em->flush();

                $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'The price successfully updated!');

                $payload=array();
                $payload['status']='ok';
                $payload['page']='refresh';
                return new Response(json_encode($payload));

            }
        }

        $payload = [];
        $payload['status'] = 'ok';
        $payload['page'] = 'edit';
        $payload['html'] = $this->renderView('price/edit.html.twig', [
            'price' => $price,
            'form' => $form->createView()
        ]);

        return new Response(json_encode($payload));
    }


    /**
     * Deletes a price entity.
     *
     * @Route("/{id}/delete", name="price_delete")
     * @Method("GET")
     * @param Price $price
     * @return Response
     */
    public function deleteAction(Request $request, Price $price)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($price);
        $em->flush();

        $request->getSession()
            ->getFlashBag()
            ->add('success', 'The price successfully deleted!');

        $payload=array();
        $payload['status']='ok';
        $payload['page']='refresh';
        return new Response(json_encode($payload));

    }

    //================================================================================================================//
    //=== Other function =============================================================================================//
    //================================================================================================================//

    /**
     * form builder Days
     * @param $form
     * @param $days
     * @return Form
     */
    public function buildFormDays($form, $days)
    {
        /* @var Form $form */
        $listDays = $this->listAllDays($days);

        $form
            ->add('day', ChoiceType::class, [
                    'choices' => $listDays,
                    'mapped' => false,
                    'multiple' => true,
                ]
            )
        ;

        return $form;
    }

    /**
     * list all days in array
     * @param $days
     * @return array
     */
    public function listAllDays($days)
    {
        $daysAll = [];
        foreach ($days as $day) {
            $dayByName = $day->getName();
            $daysAll[$dayByName] = $day->getId();
        }

        return $daysAll;
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


    /**
     * Form builder field by center
     * @param $form
     * @param $center
     * @return \Symfony\Component\Form\Form
     */
    public function buildFormFields($form, $center)
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


}
