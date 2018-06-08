<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Event;
use AppBundle\Entity\User;
use AppBundle\Form\EventType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


/**
 *
 * Event Controller
 *
 * Class EventController
 * @Route("/event")
 * @package AppBundle\Controller
 */
class EventController extends Controller
{

    /**
     * Lists all Event entities.
     * @Route("/", name="event_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        // Tous les clients uniquement pour les role_super_administrateur
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $events = $em->getRepository('AppBundle:Event')->findAll();

        } else {
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $center = $userLogin->getCenter();
            $events = $em->getRepository('AppBundle:Event')->findBy(array('center' => $center));

        }

        return $this->render('event/index.html.twig', array(
            'events' => $events
        ));
    }

    /**
     * Show Event entities details .
     * @Route("/{id}/show", name="event_show")
     * @Method("GET")
     * @param Event $event
     * @return Response
     */
    public function showAction(Event $event)
    {
        $payload=array();
        $payload['status']='ok';
        $payload['page']='show';
        $payload['html'] = $this->renderView('event/show.html.twig', array(
            'event' => $event
        ));

        return new Response(json_encode($payload));
    }

    /**
     * Creates a new event entity.
     * @Route("/new", name="event_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $event = new Event();

        $form = $this->createForm(EventType::class, $event, array(
            'action' => $this->generateUrl('event_new')
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // upload image for event
            $imgName = null;
            $imgExtension = null;
            $img = $request->files->all();
            $dataImg = isset($img['event']['image'])? $img['event']['image'] : [];

            $originName = $dataImg->getClientOriginalName();
            $imgName = md5(uniqid()). '.'. $dataImg->guessExtension();
            $dataImg->move( $this->container->getParameter('event_img'), $imgName );
            // $file->setName($dataName);
            $event->setImage($imgName);

            $em->persist($event);
            $em->flush();

//            $request->getSession()
//                ->getFlashBag()
//                ->add('success', 'The session successfully created!');

            $payload=array();
            $payload['status']='ok';
            $payload['page']='refresh';
            return new Response(json_encode($payload));

        }

        $payload=array();
        $payload['status']='ok';
        $payload['page']='new';
        $payload['html'] = $this->renderView('Event/new.html.twig', array(
            'form' => $form->createView(),
        ));

        return new Response(json_encode($payload));
    }

    /**
     * Displays a form to edit an existing event entity.
     * @Route("/{id}/edit", name="event_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Event $event
     * @return Response
     */
    public function editAction(Request $request, Event $event)
    {
        if (null === $this->getUser()) {
            throw $this->createAccessDeniedException(User::USER_IS_NOT_LOGGED_IN);
        }

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(EventType::class, $event, array(
            'action' => $this->generateUrl('event_edit',array('id'=>$event->getId()))
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                // delete the old image for event
                $file_path = $this->container->getParameter('event_img').'/'.$event->getImage();
                if(file_exists($file_path)){
                    unlink($file_path);
                }
                // update image for event uploaded
                $imgName = null;
                $imgExtension = null;
                $img = $request->files->all();
                if (isset($img['event']['image'])){
                    // upload the new image for event
                    $dataImg = $img['event']['image'];
                    $imgName = md5(uniqid()). '.'. $dataImg->guessExtension();
                    $dataImg->move( $this->container->getParameter('event_img'), $imgName );
                    $event->setImage($imgName);
                }

                $em->persist($event);
                $em->flush();

//                $request->getSession()
//                    ->getFlashBag()
//                    ->add('success', 'The Event successfully updated!');

                $payload=array();
                $payload['status']='ok';
                $payload['page']='refresh';
                return new Response(json_encode($payload));

            }
        }

        $payload = [];
        $payload['status'] = 'ok';
        $payload['page'] = 'edit';
        $payload['html'] = $this->renderView('event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView()
        ]);

        return new Response(json_encode($payload));
    }

    /**
     * disable to published event entity.
     * @Route("/{id}/published/disable", name="event_disable_published")
     * @Method({"GET"})
     * @param Event $event
     * @return Response
     */
    public function disableAction(Request $request, Event $event)
    {
        $em = $this->getDoctrine()->getManager();

        $event->setIsPublished('0');

        $em->persist($event);
        $em->flush();

        $request->getSession()
            ->getFlashBag()
            ->add('success', 'The event successfully disable to published!');

        $payload = [];
        $payload['status'] = 'ok';
        $payload['page'] = 'disable';

        return new Response(json_encode($payload));

    }

    /**
     * enable to published Event entity.
     * @Route("/{id}/published/enable", name="event_enable_published")
     * @Method({"GET"})
     * @param Event $event
     * @return Response
     */
    public function enableAction(Request $request, Event $event)
    {
        $em = $this->getDoctrine()->getManager();

        $event->setIsPublished('1');

        $em->persist($event);
        $em->flush();

        $request->getSession()
            ->getFlashBag()
            ->add('success', 'The center successfully enable to published!');

        $payload = [];
        $payload['status'] = 'ok';
        $payload['page'] = 'enable';

        return new Response(json_encode($payload));

    }

    /**
     * Deletes a Event entity.
     * @Route("/{id}/delete", name="event_delete")
     * @Method("GET")
     * @param Event $event
     * @return Response
     */
    public function deleteAction(Request $request, Event $event)
    {
        $em = $this->getDoctrine()->getManager();

        $file_path = $this->container->getParameter('event_img').'/'.$event->getImage();
        if(file_exists($file_path)){
            unlink($file_path);
        }

        $em->remove($event);
        $em->flush();

        $request->getSession()
            ->getFlashBag()
            ->add('success', 'The event successfully deleted!');

        $payload=array();
        $payload['status']='ok';
        $payload['page']='refresh';
        return new Response(json_encode($payload));

    }

}
