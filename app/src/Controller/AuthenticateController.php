<?php

namespace App\Controller;

use App\Entity\Tokens;
use App\Entity\User;
use App\Repository\TokensRepository;
use App\Security\TokenAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthenticateController extends AbstractController
{
    /**
     * @Route("/api/users", name="users")
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

    /**
     * @Route(path="/api/session", name="session_destroy", methods={delete})
     */
    public function sessionDestroy(Request $request, TokensRepository $tokenRepository)
    {
        $tokenRepository->createQueryBuilder('t')
            ->andWhere('t.token != :token')
            ->andWhere('t.user_id = :user')
            ->setParameter('token', $request->headers->get('X-AUTH-TOKEN'))
            ->setParameter('user', $request->attributes->get('user')->getId())
            ->delete()
        ;
    }
}
