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
    public function index(Request $request):Response
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

        $user = $request->attributes->get('user');

        return new JsonResponse([
            'username' => $user->getUsername(),
            'payload' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'full_name' => $user->getFullName(),
            ],
        ]);
    }

    /**
     * @Route(path="/api/session", name="session_destroy", methods={"delete"})
     * Destroy all session except current
     */
    public function destroy(Request $request, TokensRepository $tokenRepository)
    {
        $query = $tokenRepository->createQueryBuilder('t')
            ->delete()
            ->andwhere('t.token != :token')
            ->andwhere('t.user = :user')
            ->setParameter('token', $request->headers->get('X-AUTH-TOKEN'))
            ->setParameter('user', $request->attributes->get('user')->getId())
        ;
        $result = $query->getQuery()->getResult();

        return new JsonResponse([
            'success' => true,
            'count' => $result,
        ]);
    }
}
