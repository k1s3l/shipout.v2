<?php

namespace App\Controller;

use App\Entity\Factory;
use App\Form\FactoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default")
     */
    public function index(): Response
    {
        $factories = $this->getDoctrine()->getRepository(Factory::class);
        $form = $this->createForm(FactoryType::class);

        return $this->render('default/index.html.twig', ['factories' => $factories->findAll(), 'form' => $form->createView()]);
    }
}
