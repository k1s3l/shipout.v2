<?php

namespace App\Security;

use App\Repository\TokensRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class TokenAuthenticator extends AbstractAuthenticator
{
    private $tokensRepository;

    public function __construct(TokensRepository $tokensRepository)
    {
        $this->tokensRepository = $tokensRepository;
    }

    public function authenticate(Request $request): PassportInterface
    {
        $headersToken = $request->headers->get('X-AUTH-TOKEN');

        if (!$headersToken) {
            throw new CustomUserMessageAuthenticationException('roflanebalo');
        }

        $date = (new \DateTime())->format('Y-m-d\TH:i:sP');

        $apiToken = $this->tokensRepository
            ->begin()
            ->findByNotExpiredDate($date)
            ->findByToken($headersToken)
            ->getOneOrNullResult()
        ;

        if (!$apiToken) {
            throw new CustomUserMessageAuthenticationException('Zyabl, token invalid');
        }

        $user = $apiToken->getUser();
        $request->attributes->set('user', $user);

        //The first argument is used only because the implementation requires it.
        //The user's instance is returned already in the second argument
        //(well, why make a second request if the user has already been received?
        //And what will you do to me, I'm in another city)

        return new SelfValidatingPassport(new UserBadge($user->getId(), fn() => ($user)));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return new JsonResponse([
            'errors' => [
                $exception->getMessageKey(),
            ],
        ], JsonResponse::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    /**
     * X-AUTH-TOKEN required
     * if necessary use "return $this->request->headers->has('X-AUTH-TOKEN')"
     */
    public function supports(Request $request): ?bool
    {
        return true;
    }
}