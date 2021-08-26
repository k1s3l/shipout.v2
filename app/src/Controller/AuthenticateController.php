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
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AuthenticateController extends AbstractController
{
    /**
     * @Route("/api/user", name="user")
     */
    public function index(Request $request):Response
    {
        $user = $request->attributes->get('user');

        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => fn ($object, $format, $context) => $object->getId(),
        ];
        $normalizer = new ObjectNormalizer(
            null, null,
            null, null,
            null, null,
            $defaultContext
        );

        $serializer = new Serializer([$normalizer], [new JsonEncoder()]);
        $json = $serializer->serialize($user, 'json');

        return JsonResponse::fromJsonString($json);
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
