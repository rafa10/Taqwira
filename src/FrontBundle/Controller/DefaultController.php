<?php

namespace FrontBundle\Controller;

use AppBundle\Entity\Contact;
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

            return $this->redirect('contact_us');

        }

        return $this->render('FrontBundle:Default:contact_us.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /** ============================================================================================================== */
    /** === Form Builder search ====================================================================================== */
    /** ============================================================================================================== */


}
