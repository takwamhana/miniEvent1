<?php

namespace App\Controller\Api;

use App\Repository\UserRepository;
use App\Service\JwtService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/token')]
class TokenController extends AbstractController
{
    public function __construct(
        private JwtService $jwtService,
        private UserRepository $userRepository,
    ) {
    }

    #[Route('/refresh', name: 'api_token_refresh', methods: ['POST'])]
    public function refresh(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $refreshToken = $data['refresh_token'] ?? null;

        if (!$refreshToken || !is_string($refreshToken)) {
            return new JsonResponse(['error' => 'Refresh token required'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $payload = $this->jwtService->decodeRefreshToken($refreshToken);
            $email = $payload->sub ?? null;
            if (!$email) {
                return new JsonResponse(['error' => 'Invalid refresh token'], Response::HTTP_UNAUTHORIZED);
            }

            $user = $this->userRepository->findOneBy(['email' => $email]);
            if (!$user) {
                return new JsonResponse(['error' => 'Invalid refresh token'], Response::HTTP_UNAUTHORIZED);
            }

            return new JsonResponse([
                'token' => $this->jwtService->createAccessToken($user),
                'refresh_token' => $this->jwtService->createRefreshToken($user),
            ]);
        } catch (\Throwable) {
            return new JsonResponse(['error' => 'Invalid refresh token'], Response::HTTP_UNAUTHORIZED);
        }
    }
}
