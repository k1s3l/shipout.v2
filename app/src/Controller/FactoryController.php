<?php

namespace App\Controller;

use App\Entity\Factory;
use App\Form\FactoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FactoryController extends AbstractController
{
    /**
     * @Route("/api/factory", name="factory")
     */
    public function index(Request $request): Response
    {
        $data = $request->request;

        $form = $this->createForm(FactoryType::class);
        foreach ($form->all() as $child) {
            $fieldName = $child->getName();

            if (!$data->has($fieldName)) {
                return $this->json(['error' => "Заполните поле <{$fieldName}>"], 400);
            }
        }

        $state = FactoryType::stateChoices();
        $data = $data->all();
        $factory = new Factory();
        $factory
            ->setAddress($data['address'])
            ->setName($data['name'])
            ->setState($state[$data['state']]);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($factory);
        $entityManager->flush();

        return $this->json(['success' => true]);
    }
}
