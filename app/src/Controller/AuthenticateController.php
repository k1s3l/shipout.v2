<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\TokenAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthenticateController extends AbstractController
{
    public $tokenAuthenticator;

    public function __construct(TokenAuthenticator $tokenAuthenticator)
    {
        $this->tokenAuthenticator = $tokenAuthenticator;
    }

    /**
     * @Route("/api/users", name="authenticate")
     */
    public function index():Response
    {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findBy(
                ['is_verified' => true],
                ['id' => 'DESC'],
                10
            );

        /**
         * @TODO Добавить сериалайзер
         */

        return new JsonResponse([
            'users' => [
                'user',
                'user',
                'user',
            ],
        ]);
    }
}
