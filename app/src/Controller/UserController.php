<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\TokensRepository;
use App\Serializer\Normalizer\UserNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
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

    /**
     * @Route(path="/api/sign_up",name="sign_up",methods={"post"})
     */
    public function signUp(Request $request, ValidatorInterface $validator)
    {
        $data = $request->toArray();
        $errors = $validator->validate((new User())->setPassword($data['password']));
        $errorMsg = [];

        for ($i = 0; ; $i++) {
            if ($errors->has($i)) {
                $error = $errors->get($i);
                $errorMsg['errors'][$error->getPropertyPath()] = $error->getMessage();
                continue;
            }
            break;
        }

        return new JsonResponse($errorMsg);
    }


    /**
     * @Route(path="/api/sign_in",name="sign_in",methods={"post"})
     */
    public function signIn(Request $request)
    {
        return new JsonResponse(['message' => 'string']);
    }

    /**
     * @Route(path="/api/confirm",name="confirm_code",methods={"patch"})
     */
    public function confirm(Request $request)
    {
        return new JsonResponse(['message' => 'string']);
    }
}
