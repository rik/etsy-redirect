<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use AppBundle\Entity\Redirect;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $redirect = new Redirect();

        $form = $this->createFormBuilder($redirect)
            ->add('url', UrlType::class)
            ->add('save', SubmitType::class, array('label' => 'Shorten'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $redirect = $form->getData();
            $redirect->setTotalViews(0);

            $em = $this->getDoctrine()->getManager();
            $em->persist($redirect);
            // FIXME Cache exception and return slug of a previously known URL
            $em->flush();

            return $this->redirectToRoute('preview', array('slug' => $redirect->getSlug()));
        }

        return $this->render('default/index.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/preview/{slug}", name="preview")
    */
    public function previewAction($slug)
    {
        $redirect = $this->getDoctrine()
            ->getRepository('AppBundle:Redirect')
            ->find($slug);

        if (!$redirect) {
            throw $this->createNotFoundException(
                'This redirection does not exist'
            );
        }

        return $this->render('default/preview.html.twig', array(
            'redirect' => $redirect,
        ));
    }
}
