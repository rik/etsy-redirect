<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

use AppBundle\Entity\Redirect;

class DefaultController extends Controller
{
    const UUID_REGEXP = '^(?i:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})$';

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $redirect = new Redirect();

        $form = $this->createFormBuilder($redirect)
            ->add('url', UrlType::class, array('label' => 'Paste a link to shorten it'))
            ->add('save', SubmitType::class, array('label' => 'Shorten'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $redirect = $form->getData();
            $redirect->setTotalViews(0);

            $em = $this->getDoctrine()->getManager();
            $em->persist($redirect);
            try {
                $em->flush();
            } catch (UniqueConstraintViolationException $e) {
                $redirect = $this->getDoctrine()
                    ->getRepository('AppBundle:Redirect')
                    ->findOneByUrl($redirect->getUrl());
            }

            return $this->redirectToRoute('preview', array('slug' => $redirect->getSlug()));
        }

        return $this->render('default/index.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/preview/{slug}", name="preview", requirements={"slug": DefaultController::UUID_REGEXP})
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

    /**
     * @Route("/{slug}", name="redirect", requirements={"slug": DefaultController::UUID_REGEXP})
     */
    public function redirectAction($slug)
    {
        $redirect = $this->getDoctrine()
            ->getRepository('AppBundle:Redirect')
            ->find($slug);

        if (!$redirect) {
            throw $this->createNotFoundException(
                'This redirection does not exist'
            );
        }

        $em = $this->getDoctrine()->getManager();
        $sql = <<<'SQL'
            UPDATE AppBundle\Entity\Redirect redirect
            SET redirect.totalViews = redirect.totalViews + 1
            WHERE redirect.slug = :slug
SQL;
        $query = $em->createQuery($sql);
        $query->setParameter('slug', $redirect->getSlug());
        $query->execute();

        return $this->redirect($redirect->getUrl());
    }
}
