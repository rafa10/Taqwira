<?php

namespace FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
        $form = $this->formBuilderSearch();

        return $this->render('FrontBundle:Default:index.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Auto-complete form field region & ville & center
     * @Route("form/search", name="form_search")
     */
    public function getFormSearchAction()
    {

        $regions = [];
        $citys = [];
        $centers = [];
        $em = $this->getDoctrine()->getManager();

        $regionAll = $em->getRepository('AppBundle:Region')->findAll();
        foreach ($regionAll as $region){
            $regions[$region->getName()] = null;
        }
        $cityAll = $em->getRepository('AppBundle:City')->findAll();
        foreach ($cityAll as $city){
            $citys[$city->getName()] = null;
        }

        $centerAll = $em->getRepository('AppBundle:Center')->findAll();
        foreach ($centerAll as $center){
            $centers[$center->getName()] = null;
        }

        $regionCity = array_merge($regions, $citys);

        $payload=array();
        $payload['status']='ok';
        $payload['page']='show';
        $payload['region_city'] = $regionCity;
        $payload['center'] = $centers;

        return new Response(json_encode($payload));
    }



    /** ============================================================================================================== */
    /** === Form Builder search ====================================================================================== */
    /** ============================================================================================================== */

    /**
     * form builder search
     * @return mixed
     */
    public function formBuilderSearch()
    {
        $form = $this->createFormBuilder()
            ->add('ville', SearchType::class, array(
                'attr' => array(
                    'class' => 'autocomplete-region',
                )
            ))
            ->add('center', SearchType::class, array(
                'attr' => array(
                    'class' => 'autocomplete-center',
                )
            ))
            ->add('date', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'required' => true,
                'model_timezone' => 'Europe/Paris',
                'attr' => array(
                    'class' => 'datepicker',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'dd-mm-yyyy'
                )
            ))
            ->getForm();

        return $form;

    }

}
