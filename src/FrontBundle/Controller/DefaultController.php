<?php

namespace FrontBundle\Controller;

use AppBundle\Entity\Contact;
use AppBundle\Entity\Notification;
use AppBundle\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 * @Route("/")
 * @package FrontBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @Route("", name="home")
     */
    public function indexAction()
    {
        return $this->render('FrontBundle:Default:index.html.twig', array(
        ));
    }

    /**
     * contact for create new center
     * @Route("contact_us", name="contact_us")
     * @Method({"GET", "POST"})
     */
    public function contactAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact, array(
            'action' => $this->generateUrl('contact_us')
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($contact);
            $em->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'Le message a bien été envoyé!');

            // Create notification for dashboard admin center
            $this->container->get('app.notification')->newNotificationMessage($subject=Notification::MESSAGE, $link=Notification::CONTACT_LINK);

            return $this->redirect('contact_us');

        }

        return $this->render('FrontBundle:Default:contact_us.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /** ============================================================================================================== */
    /** === Form Builder search ====================================================================================== */
    /** ============================================================================================================== */
    /**
     * Get city by region for center.
     * @Route("/register/region/{id}", name="get_city_by_region")
     * @Method("GET")
     * @return Response
     */
    function getCityByRegionAction($id)
    {
        $city = null;
        $em = $this->getDoctrine()->getManager();
//        $city = $em->getRepository('AppBundle:Region')->findBy(array('region' => $region));

//        if ($request->get('id')) {
//            $regionId = $request->get('id');
            $region = $em->getRepository('AppBundle:Region')->find($id);
            $city = $em->getRepository('AppBundle:City')->findBy(array('region' => $region));
//        }

        $payload=array();
        $payload['status']='ok';
        $payload['page']='show';
        $payload['html'] = $this->renderView('FrontBundle:Default:form_city.html.twig', [
            'city' => $city,
        ]);

        return new Response(json_encode($payload));
    }

}
