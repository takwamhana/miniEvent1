<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\WebauthnCredential;
use App\Repository\UserRepository;
use App\Repository\WebauthnCredentialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasskeyAuthService
{
    public function __construct(
        private UserRepository $userRepository,
        private WebauthnCredentialRepository $credentialRepository,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private JwtService $jwtService,
    ) {
    }

    public function getRegistrationOptions(Request $request): array
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;

        if (!$email) {
            throw new \InvalidArgumentException('Email is required');
        }

        // Mock WebAuthn registration options
        return [
            'challenge' => bin2hex(random_bytes(32)),
            'rp' => [
                'name' => 'Mini Event App',
                'id' => 'localhost'
            ],
            'user' => [
                'id' => base64_encode($email),
                'name' => $email,
                'displayName' => $email
            ],
            'pubKeyCredParams' => [
                ['alg' => -7, 'type' => 'public-key'],
                ['alg' => -257, 'type' => 'public-key']
            ],
            'timeout' => 60000,
            'attestation' => 'none'
        ];
    }

    public function verifyRegistration(Request $request): User
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $credentialId = $data['credentialId'] ?? null;
        $publicKey = $data['publicKey'] ?? null;

        if (!$email || !$credentialId || !$publicKey) {
            throw new \InvalidArgumentException('Missing required fields');
        }

        // Check if user exists
        $user = $this->userRepository->findOneBy(['email' => $email]);
        
        if (!$user) {
            // Create new user
            $user = new User();
            $user->setEmail($email);
            $user->setRoles(['ROLE_USER']);
            $this->entityManager->persist($user);
        }

        // Create WebAuthn credential
        $credential = new WebauthnCredential();
        $credential->setUser($user);
        $credential->setCredentialId($credentialId);
        $credential->setType('public-key');
        $credential->setPublicKey($publicKey);
        $credential->setUserHandle(base64_encode($email));
        $credential->setSignatureCounter('0');
        $credential->setUvInitialized(false);
        $credential->setTransports('[]');
        $credential->setAttestationType('none');
        $credential->setTrustPath('{}');
        $credential->setAaguid('');

        $this->entityManager->persist($credential);
        $this->entityManager->flush();

        return $user;
    }

    public function getLoginOptions(Request $request): array
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;

        if (!$email) {
            throw new \InvalidArgumentException('Email is required');
        }

        // Mock WebAuthn login options
        return [
            'challenge' => bin2hex(random_bytes(32)),
            'allowCredentials' => [
                [
                    'id' => 'mock-credential-id',
                    'type' => 'public-key',
                    'transports' => ['internal', 'usb', 'nfc', 'ble']
                ]
            ],
            'timeout' => 60000,
            'userVerification' => 'preferred'
        ];
    }

    public function verifyLogin(Request $request): User
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $credentialId = $data['credentialId'] ?? null;

        if (!$email || !$credentialId) {
            throw new \InvalidArgumentException('Missing required fields');
        }

        $user = $this->userRepository->findOneBy(['email' => $email]);
        
        if (!$user) {
            throw new \InvalidArgumentException('User not found');
        }

        // In a real implementation, you would verify the WebAuthn assertion here
        // For now, we'll just check if the user exists
        return $user;
    }

    public function generateTokens(User $user): array
    {
        return [
            'token' => $this->jwtService->createAccessToken($user),
            'refresh_token' => $this->jwtService->createRefreshToken($user),
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ],
        ];
    }
}
