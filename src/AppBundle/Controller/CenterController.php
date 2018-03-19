<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Center;
use AppBundle\Entity\User;
use AppBundle\Form\CenterType;
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
 * Class CenterController
 * @Route("/center")
 * @package AppBundle\Controller
 */
class CenterController extends Controller
{

    /**
     * Lists all Center entities.
     *
     * @Route("/", name="center_index")
     * @Method("GET")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function indexCenterAction()
    {
        $em = $this->getDoctrine()->getManager();

        $centers = $em->getRepository('AppBundle:Center')->findAll();

        return $this->render('center/index.html.twig', array(
            'centers' => $centers
        ));
    }


    /**
     * Creates a new Center entity.
     *
     * @Route("/new", name="center_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param Request $request
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $center = new Center();

        $form = $this->createForm(CenterType::class, $center, array(
            'action' => $this->generateUrl('center_new')
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // upload avatar center
            $imgName = null;
            $imgExtension = null;
            $img = $request->files->all();
            $dataImg = isset($img['center']['avatar'])? $img['center']['avatar'] : [];

            $originName = $dataImg->getClientOriginalName();
            $imgName = md5(uniqid()). '.'. $dataImg->guessExtension();
            $dataImg->move( $this->container->getParameter('avatar'), $imgName );
            // $file->setName($dataName);
            $center->setAvatar($imgName);

            $em->persist($center);
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
        $payload['html'] = $this->renderView('center/new.html.twig', array(
            'form' => $form->createView(),
        ));

        return new Response(json_encode($payload));
    }

    /**
     * Displays a form to edit an existing Center entity.
     *
     * @Route("/{id}/edit", name="center_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN')")
     * @param Request $request
     * @param Center $center
     * @return Response
     */
    public function editAction(Request $request, Center $center)
    {
        if (null === $this->getUser()) {
            throw $this->createAccessDeniedException(User::USER_IS_NOT_LOGGED_IN);
        }
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(CenterType::class, $center, array(
            'action' => $this->generateUrl('center_edit',array('id'=>$center->getId()))
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // update avatar uploaded
                $imgName = null;
                $imgExtension = null;
                $img = $request->files->all();
                if (isset($img['center']['avatar'])){
                    $dataImg = $img['center']['avatar'];
                    $imgName = md5(uniqid()). '.'. $dataImg->guessExtension();
                    $dataImg->move( $this->container->getParameter('avatar'), $imgName );
                    $center->setAvatar($imgName);
                }

                $em->persist($center);
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
        $payload['html'] = $this->renderView('center/edit.html.twig', [
            'center' => $center,
            'form' => $form->createView()
        ]);

        return new Response(json_encode($payload));
    }

    /**
     * disable existing Center entity.
     *
     * @Route("/{id}/disable", name="center_disable")
     * @Method({"GET"})
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param Center $center
     * @return Response
     */
    public function disableAction(Request $request, Center $center)
    {
        $em = $this->getDoctrine()->getManager();

        $center->setIsActive('0');

        $em->persist($center);
        $em->flush();

        $request->getSession()
            ->getFlashBag()
            ->add('success', 'The center successfully disabled!');

        $payload = [];
        $payload['status'] = 'ok';
        $payload['page'] = 'refresh';

        return new Response(json_encode($payload));

    }

    /**
     * enable existing Center entity.
     *
     * @Route("/{id}/enable", name="center_enable")
     * @Method({"GET"})
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param Center $center
     * @return Response
     */
    public function enableAction(Request $request, Center $center)
    {
        $em = $this->getDoctrine()->getManager();

        $center->setIsActive('1');

        $em->persist($center);
        $em->flush();

        $request->getSession()
            ->getFlashBag()
            ->add('success', 'The center successfully enabled!');

        $payload = [];
        $payload['status'] = 'ok';
        $payload['page'] = 'refresh';

        return new Response(json_encode($payload));

    }

    /**
     * Deletes a Center entity.
     *
     * @Route("/{id}/delete", name="center_delete")
     * @Method("GET")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param Center $center
     * @return Response
     */
    public function deleteAction(Request $request, Center $center)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($center);
        $em->flush();

        $request->getSession()
            ->getFlashBag()
            ->add('success', 'The company successfully deleted!');

        $payload=array();
        $payload['status']='ok';
        $payload['page']='refresh';
        return new Response(json_encode($payload));

    }

}
