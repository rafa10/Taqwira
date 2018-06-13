<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Image;
use AppBundle\Form\ImageType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * Image Controller
 *
 * Class ImageController
 * @Route("/image")
 * @package AppBundle\Controller
 */
class ImageController extends Controller
{

    /**
     * Lists all Image entities.
     *
     * @Route("/", name="image_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        // Tous les clients uniquement pour les role_super_administrateur
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $images = $em->getRepository('AppBundle:Image')->findAll();

        } else {
            $userLogin = $this->get('security.token_storage')->getToken()->getUser();
            $center = $userLogin->getCenter();
            $images = $em->getRepository('AppBundle:Image')->findBy(array('center' => $center));

        }

        return $this->render('image/index.html.twig', array(
            'images' => $images
        ));
    }

    /**
     * Creates a new Image entity.
     *
     * @Route("/new", name="image_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $image = new Image();

        $form = $this->createForm(ImageType::class, $image, array(
            'action' => $this->generateUrl('image_new')
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // upload image center
            $imgName = null;
            $imgExtension = null;
            $img = $request->files->all();
            $dataImg = isset($img['image']['url'])? $img['image']['url'] : null;

            $originName = $dataImg->getClientOriginalName();
            $imgName = md5(uniqid()). '.'. $dataImg->guessExtension();
            $dataImg->move( $this->container->getParameter('photo_directory'), $imgName );

            $image->setName($originName);
            $image->setUrl($imgName);

            $em->persist($image);
            $em->flush();

            $payload=array();
            $payload['status']='ok';
            $payload['page']='refresh';
            return new Response(json_encode($payload));

        }

        $payload=array();
        $payload['status']='ok';
        $payload['page']='new';
        $payload['html'] = $this->renderView('image/new.html.twig', array(
            'form' => $form->createView(),
        ));

        return new Response(json_encode($payload));
    }

    /**
     * Deletes a Image entity.
     *
     * @Route("/{id}/delete", name="image_delete")
     * @Method("GET")
     * @param Image $image
     * @return Response
     */
    public function deleteAction(Request $request, Image $image)
    {
        $em = $this->getDoctrine()->getManager();

        $file_path = $this->container->getParameter('photo_directory').'/'.$image->getUrl();
        if(file_exists($file_path)){
            unlink($file_path);
        }

        $em->remove($image);
        $em->flush();

        $payload=array();
        $payload['status']='ok';
        $payload['page']='refresh';
        return new Response(json_encode($payload));

    }

}
