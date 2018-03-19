<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Customer;
use AppBundle\Entity\User;
use AppBundle\Form\CustomerType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


/**
 *
 * Customer Controller
 *
 * Class CustomerController
 * @Route("/customer")
 * @package AppBundle\Controller
 */
class CustomerController extends Controller
{

    /**
     * Lists all customer entities.
     * @Route("/", name="customer_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        // Tous les clients uniquement pour les role_super_administrateur
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $customers = $em->getRepository('AppBundle:Customer')->findAll();

        } else {
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $center = $userLogin->getCenter();
            $customers = $em->getRepository('AppBundle:Customer')->findBy(array('center' => $center));

        }

        return $this->render('customer/index.html.twig', array(
            'customers' => $customers
        ));
    }


    /**
     * Creates a new session entity.
     * @Route("/new", name="customer_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $customer = new Customer();

        $form = $this->createForm(CustomerType::class, $customer, array(
            'action' => $this->generateUrl('customer_new')
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($customer);
            $em->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'The session successfully created!');

            $payload=array();
            $payload['status']='ok';
            $payload['page']='refresh';
            return new Response(json_encode($payload));

        }

        $payload=array();
        $payload['status']='ok';
        $payload['page']='new';
        $payload['html'] = $this->renderView('customer/new.html.twig', array(
            'form' => $form->createView(),
        ));

        return new Response(json_encode($payload));
    }

    /**
     * Displays a form to edit an existing session entity.
     * @Route("/{id}/edit", name="customer_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Customer $customer
     * @return Response
     */
    public function editAction(Request $request, Customer $customer)
    {
        if (null === $this->getUser()) {
            throw $this->createAccessDeniedException(User::USER_IS_NOT_LOGGED_IN);
        }
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(CustomerType::class, $customer, array(
            'action' => $this->generateUrl('customer_edit',array('id'=>$customer->getId()))
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $em->persist($customer);
                $em->flush();

                $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'The customer successfully updated!');

                $payload=array();
                $payload['status']='ok';
                $payload['page']='refresh';
                return new Response(json_encode($payload));

            }
        }

        $payload = [];
        $payload['status'] = 'ok';
        $payload['page'] = 'edit';
        $payload['html'] = $this->renderView('customer/edit.html.twig', [
            'customer' => $customer,
            'form' => $form->createView()
        ]);

        return new Response(json_encode($payload));
    }


    /**
     * Deletes a customer entity.
     * @Route("/{id}/delete", name="customer_delete")
     * @Method("GET")
     * @param Customer $customer
     * @return Response
     */
    public function deleteAction(Request $request, Customer $customer)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($customer);
        $em->flush();

        $request->getSession()
            ->getFlashBag()
            ->add('success', 'The customer successfully deleted!');

        $payload=array();
        $payload['status']='ok';
        $payload['page']='refresh';
        return new Response(json_encode($payload));

    }

}
