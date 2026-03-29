<?php

require 'vendor/autoload.php';

use App\Entity\User;

$kernel = new \App\Kernel('dev', true);
$kernel->boot();

// Get the entity manager directly from Doctrine
$connection = $kernel->getContainer()->get('doctrine.dbal.default_connection');

// Simple SQL insert with proper escaping
$sql = 'INSERT INTO "user" (email, roles, password) VALUES (?, ?, ?)';
$stmt = $connection->prepare($sql);
$stmt->bindValue(1, 'admin@example.com');
$stmt->bindValue(2, '["ROLE_ADMIN"]');
$stmt->bindValue(3, null);
$stmt->executeStatement();

echo "Admin user created successfully!\n";
