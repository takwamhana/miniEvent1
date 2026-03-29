<?php

require 'vendor/autoload.php';

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

$kernel = new \App\Kernel('dev', true);
$kernel->boot();

$entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');

$user = new User();
$user->setEmail('admin@example.com');
$user->setRoles(['ROLE_ADMIN']);

$entityManager->persist($user);
$entityManager->flush();

echo "Admin user created successfully!\n";
