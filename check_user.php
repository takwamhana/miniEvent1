<?php

require 'vendor/autoload.php';

$kernel = new \App\Kernel('dev', true);
$kernel->boot();

$connection = $kernel->getContainer()->get('doctrine.dbal.default_connection');

// Check if admin user exists
$result = $connection->fetchAllAssociative('SELECT * FROM "user"');
echo "Users in database:\n";
print_r($result);
