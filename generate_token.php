<?php

require 'vendor/autoload.php';

use App\Entity\User;
use App\Repository\UserRepository;

$kernel = new \App\Kernel('dev', true);
$kernel->boot();

$entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');
$userRepository = $entityManager->getRepository(User::class);
$user = $userRepository->findOneBy(['email' => 'admin@example.com']);

if ($user) {
    // Generate token manually since PasskeyAuthService needs UserRepository
    if (in_array('ROLE_ADMIN', $user->getRoles())) {
        $accessToken = 'admin-token-' . bin2hex(random_bytes(16));
    } else {
        $accessToken = 'user-token-' . bin2hex(random_bytes(16));
    }
    
    $refreshToken = 'refresh-token-' . $user->getId() . '-' . time();
    
    echo "Generated token: " . $accessToken . "\n";
    echo "Use this token in Authorization header: Bearer " . $accessToken . "\n";
} else {
    echo "Admin user not found!\n";
}
