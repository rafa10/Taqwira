<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Effective;
use AppBundle\Entity\User;
use AppBundle\Form\EffectiveType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Effective controller.
 *
 * @Route("/effective")
 */
class EffectiveController extends Controller
{
    /**
     * Lists all effective entities.
     * @Route("/", name="effective_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        // Tous les clients uniquement pour les role_super_administrateur
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $effectives = $em->getRepository('AppBundle:Effective')->findAll();

        } else {
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $center = $userLogin->getCenter();
            $effectives = $em->getRepository('AppBundle:Effective')->findBy(array('center' => $center));

        }

        return $this->render('effective/index.html.twig', array(
            'effectives' => $effectives,
        ));
    }

    /**
     * Creates a new effective entity.
     * @Route("/new", name="effective_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $effective = new Effective();

        $form = $this->createForm(EffectiveType::class, $effective, array(
            'action' => $this->generateUrl('effective_new')
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($effective);
            $em->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'The effective successfully created!');

            $payload=array();
            $payload['status']='ok';
            $payload['page']='refresh';
            return new Response(json_encode($payload));

        }

        $payload=array();
        $payload['status']='ok';
        $payload['page']='new';
        $payload['html'] = $this->renderView('effective/new.html.twig', array(
            'form' => $form->createView(),
        ));

        return new Response(json_encode($payload));
    }

    /**
     * Displays a form to edit an existing effective entity.
     * @Route("/{id}/edit", name="effective_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Effective $effective
     * @return Response
     */
    public function editAction(Request $request, Effective $effective)
    {
        if (null === $this->getUser()) {
            throw $this->createAccessDeniedException(User::USER_IS_NOT_LOGGED_IN);
        }
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(EffectiveType::class, $effective, array(
            'action' => $this->generateUrl('effective_edit',array('id'=>$effective->getId()))
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $em->persist($effective);
                $em->flush();

                $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'The effective successfully updated!');

                $payload=array();
                $payload['status']='ok';
                $payload['page']='refresh';
                return new Response(json_encode($payload));

            }
        }

        $payload = [];
        $payload['status'] = 'ok';
        $payload['page'] = 'edit';
        $payload['html'] = $this->renderView('effective/edit.html.twig', [
            'effective' => $effective,
            'form' => $form->createView()
        ]);

        return new Response(json_encode($payload));
    }

    /**
     * Deletes a effective entity.
     * @Route("/{id}/delete", name="effective_delete")
     * @Method("GET")
     * @param Effective $effective
     * @return Response
     */
    public function deleteAction(Request $request, Effective $effective)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($effective);
        $em->flush();

        $request->getSession()
            ->getFlashBag()
            ->add('success', 'The effective successfully deleted!');

        $payload=array();
        $payload['status']='ok';
        $payload['page']='refresh';
        return new Response(json_encode($payload));

    }

    /**
     * Finds and displays a effective entity.
     * @Route("/{id}", name="effective_show")
     * @Method("GET")
     */
    public function showAction(Effective $effective)
    {
        $payload=array();
        $payload['status']='ok';
        $payload['page']='show';
        $payload['html'] = $this->renderView('effective/show.html.twig', array(
            'effective' => $effective,
        ));

        return new Response(json_encode($payload));
    }

}
