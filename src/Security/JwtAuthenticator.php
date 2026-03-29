<?php

namespace App\Security;

use App\Service\JwtService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class JwtAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private JwtService $jwtService,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        $path = $request->getPathInfo();

        if (!str_starts_with($path, '/api')) {
            return false;
        }
        if (str_starts_with($path, '/api/auth')) {
            return false;
        }
        if ($path === '/api/token/refresh') {
            return false;
        }
        if ($request->isMethod('GET') && preg_match('#^/api/events$#', $path)) {
            return false;
        }
        if ($request->isMethod('GET') && preg_match('#^/api/events/\d+$#', $path)) {
            return false;
        }
        if ($request->isMethod('POST') && preg_match('#^/api/events/\d+/reservations$#', $path)) {
            return false;
        }

        return true;
    }

    public function authenticate(Request $request): Passport
    {
        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        $jwt = substr($authHeader, 7);

        try {
            $payload = $this->jwtService->decodeAccessToken($jwt);
            $email = $payload->sub ?? null;
            if (!$email || !is_string($email)) {
                throw new \InvalidArgumentException('Invalid token subject');
            }

            return new SelfValidatingPassport(new UserBadge($email));
        } catch (\Throwable $e) {
            throw new CustomUserMessageAuthenticationException('Invalid token');
        }
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
