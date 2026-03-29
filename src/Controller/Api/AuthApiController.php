<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Service\PasskeyAuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/auth')]
class AuthApiController extends AbstractController
{
    private PasskeyAuthService $passkeyAuthService;

    public function __construct(PasskeyAuthService $passkeyAuthService)
    {
        $this->passkeyAuthService = $passkeyAuthService;
    }

    #[Route('/register/options', name: 'api_auth_register_options', methods: ['POST'])]
    public function registerOptions(Request $request): JsonResponse
    {
        try {
            $options = $this->passkeyAuthService->getRegistrationOptions($request);
            return new JsonResponse($options);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/register/verify', name: 'api_auth_register_verify', methods: ['POST'])]
    public function registerVerify(Request $request): JsonResponse
    {
        try {
            $user = $this->passkeyAuthService->verifyRegistration($request);
            $tokens = $this->passkeyAuthService->generateTokens($user);
            
            return new JsonResponse($tokens);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/login/options', name: 'api_auth_login_options', methods: ['POST'])]
    public function loginOptions(Request $request): JsonResponse
    {
        try {
            $options = $this->passkeyAuthService->getLoginOptions($request);
            return new JsonResponse($options);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/login/verify', name: 'api_auth_login_verify', methods: ['POST'])]
    public function loginVerify(Request $request): JsonResponse
    {
        try {
            $user = $this->passkeyAuthService->verifyLogin($request);
            $tokens = $this->passkeyAuthService->generateTokens($user);
            
            return new JsonResponse($tokens);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
