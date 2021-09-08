<?php

namespace App\Controller;

use App\Entity\Tokens;
use App\Entity\User;
use App\Repository\TokensRepository;
use App\Repository\UserRepository;
use App\Serializer\Normalizer\UserNormalizer;
use App\Services\ErrorService;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    public $passwordHasher;
    public $errorService;

    public function __construct(ErrorService $errorService, ValidatorInterface $validator, UserPasswordHasherInterface $passwordHasher, TokensRepository $tokenRepository)
    {
        $this->errorService = $errorService;
        $this->validator = $validator;
        $this->passwordHasher = $passwordHasher;
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * @Route("/api/user", name="user")
     */
    public function index(Request $request):Response
    {
        $user = $request->attributes->get('user');

        $serializer = new Serializer([new DateTimeNormalizer(), new UserNormalizer()], [new JsonEncoder()]);
        $json = $serializer->serialize($user, 'json');

        return JsonResponse::fromJsonString($json);
    }

    /**
     * @Route(path="/api/sessions", name="destroy_session_except_current", methods={"delete"})
     * Destroy all session except current
     */
    public function destroy(Request $request)
    {
        $query = $this->tokenRepository->createQueryBuilder('t')
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

    /**
     * @Route(path="/api/sign_up",name="sign_up",methods={"post"})
     */
    public function signUp(Request $request)
    {
        $user = new User();

        $serializer = new Serializer(
            [
                new DateTimeNormalizer([
                    DateTimeNormalizer::FORMAT_KEY => 'Y-m-d',
                ]),
                new UserNormalizer(
                    null,
                    null,
                    null,
                    new ReflectionExtractor(),
                ),
                new GetSetMethodNormalizer(),
                new ArrayDenormalizer(),
            ],
            [
                new JsonEncoder(),
            ]
        );

        $serializer->deserialize($request->getContent(), User::class, 'json', [
            DateTimeNormalizer::FORMAT_KEY => 'Y-m-d',
            AbstractNormalizer::OBJECT_TO_POPULATE => $user,
        ]);

        $errors = $this->errorService->getMessages($user, $this->validator);

        if (!$errors) {
            $em = $this->getDoctrine()->getManager();
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
            $em->persist($user);
            $em->flush();

            $json = $serializer->serialize($user, 'json');

            return JsonResponse::fromJsonString($json);
        }

        return new JsonResponse($errors);
    }


    /**
     * @Route(path="/api/sign_in",name="sign_in",methods={"post"})
     */
    public function signIn(Request $request, UserRepository $userRepository, EntityManagerInterface $em)
    {
        $user = $userRepository->findOneBy(['email' => $request->get('email')]);

        $date = (new \DateTimeImmutable())->modify("+15 minutes");
        $payload = [
            "user" => $user->getUsername(),
            "exp"  => $date->getTimestamp(),
        ];
        $jwt = JWT::encode($payload, 'HS256');

        $serializer = new Serializer(
            [
                new DateTimeNormalizer([
                    DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s',
                ]),
                new ObjectNormalizer(
                    null,
                    null,
                    null,
                    new ReflectionExtractor(),
                    null,
                    null,
                    [
                        AbstractNormalizer::IGNORED_ATTRIBUTES => ['user', 'id'],
                    ],
                ),
                new GetSetMethodNormalizer(),
                new ArrayDenormalizer(),
            ],
            [
                new JsonEncoder(),
            ]
        );

        $token = new Tokens();
        $serializer->deserialize($request->getContent(), Tokens::class, 'json', [
            DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s',
            AbstractNormalizer::OBJECT_TO_POPULATE => $token,
        ]);

        $token
            ->setToken($jwt)
            ->setUser($user)
            ->setExpiredAt($date)
            ->setIp($request->getClientIp())
        ;

        $errors = $this->errorService->getMessages($token, $this->validator);

        if (!$errors) {
            $em->persist($token);
            $em->flush();
            $json = $serializer->serialize($token, 'json');

            return JsonResponse::fromJsonString($json);
        }

        return new JsonResponse($errors, 403);
    }

    /**
     * @Route(path="/api/confirm",name="confirm_code",methods={"patch"})
     */
    public function confirm(Request $request)
    {
        return new JsonResponse(['message' => 'string']);
    }

    /**
     * @Route(path="/api/session/{uuid}", name="delete_session", methods={"delete"})
     */
    public function destroySession(Request $request, string $uuid)
    {
        $session = $this->tokenRepository->createQueryBuilder('t')
            ->andWhere('t.uuid = :uuid')
            ->andWhere('t.user = :user')
            ->setParameter('uuid', $uuid)
            ->setParameter('user', $request->attributes->get('user')->getId())
            ->delete()
        ;

        $result = $session->getQuery()->getResult();

        return new JsonResponse([
            'success' => true,
            'count' => $result,
        ]);
    }
}
