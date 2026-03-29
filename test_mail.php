<?php

require 'vendor/autoload.php';

use App\Service\MailerService;
use App\Entity\Reservation;
use App\Entity\Event;

$kernel = new \App\Kernel('dev', true);
$kernel->boot();

$entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');

// Create test event
$event = new Event();
$event->setTitle('Test Event');
$event->setDescription('Test Description');
$event->setDate(new \DateTime('2024-12-25'));
$event->setLocation('Test Location');
$event->setSeats(50);

// Create test reservation
$reservation = new Reservation();
$reservation->setName('John Doe');
$reservation->setEmail('john@example.com');
$reservation->setPhone('123456789');
$reservation->setEvent($event);

// Test mailer
$mailerService = $kernel->getContainer()->get(MailerService::class);

try {
    $result = $mailerService->sendReservationConfirmation($reservation);
    echo "Email sent successfully: " . ($result ? 'YES' : 'NO') . "\n";
} catch (Exception $e) {
    echo "Error sending email: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
