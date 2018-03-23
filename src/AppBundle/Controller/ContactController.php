<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Center;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Field;
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
 * Contact Controller
 *
 * Class ContactController
 * @Route("/contact")
 * @package AppBundle\Controller
 */
class ContactController extends Controller
{

    /**
     * Lists all Center entities.
     *
     * @Route("/", name="contact_index")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method("GET")
     */
    public function indexCalenderAction()
    {
        $em = $this->getDoctrine()->getManager();
        $contatcs = $em->getRepository('AppBundle:Contact')->findBy(array(), array('created' => 'DESC'));

        return $this->render('contact/index.html.twig', array(
            'contacts' => $contatcs
        ));
    }

    /**
     * Display message details.
     * @Route("/{id}/message_details/show", name="message_details_show")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method("GET")
     */
    public function getNotificationAction(Contact $contact)
    {
        $em = $this->getDoctrine()->getManager();
        $contact->setIsLocked(true);
        $em->persist($contact);
        $em->flush();


        $payload=array();
        $payload['status']='ok';
        $payload['page']='show';
        $payload['html'] = $this->renderView('contact/details.html.twig', array(
            'contact' => $contact
        ));
        return new Response(json_encode($payload));
    }

}
