<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Center;
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
 * Calendar Controller
 *
 * Class ConfigurationController
 * @Route("/configuration")
 * @package AppBundle\Controller
 */
class ConfigurationController extends Controller
{

    /**
     * Lists all Center entities.
     *
     * @Route("/", name="configuration_index")
     * @Method("GET")
     */
    public function indexConfigurationAction()
    {
        $em = $this->getDoctrine()->getManager();
        $userLogin = $this->get('security.token_storage')->getToken()->getUser();
        $center = $userLogin->getCenter();

        return $this->render('configuration/index.html.twig', array(
            'center' => $center
        ));
    }

    /**
     * enable existing Center entity.
     * @Route("/{id}/share_program/enable", name="share_program_enable")
     * @Method({"GET"})
     * @param Center $center
     * @return Response
     */
    public function enableAction(Request $request, Center $center)
    {
        $em = $this->getDoctrine()->getManager();

        $center->setShareProgram(true);

        $em->persist($center);
        $em->flush();

//        $request->getSession()
//            ->getFlashBag()
//            ->add('success', 'The share program center successfully enabled!');

        $payload = [];
        $payload['status'] = 'ok';
        $payload['page'] = 'enable';

        return new Response(json_encode($payload));

    }

    /**
     * disable existing Center entity.
     * @Route("/{id}/share_program/disable", name="share_program_disable")
     * @Method({"GET"})
     * @param Center $center
     * @return Response
     */
    public function disableAction(Request $request, Center $center)
    {
        $em = $this->getDoctrine()->getManager();

        $center->setShareProgram(false);

        $em->persist($center);
        $em->flush();

//        $request->getSession()
//            ->getFlashBag()
//            ->add('success', 'The share program center successfully disabled!');

        $payload = [];
        $payload['status'] = 'ok';
        $payload['page'] = 'disable';

        return new Response(json_encode($payload));

    }


}
