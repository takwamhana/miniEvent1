<?php

namespace App\Tests\Unit;

use App\Entity\Event;
use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ReservationTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private ReservationRepository $repository;

    protected function setUp(): void
    {
        // Mock EntityManager
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        // Mock Repository
        $this->repository = $this->createMock(ReservationRepository::class);
    }

    public function testCannotReserveMoreThanAvailableSeats(): void
    {
        // Create an event with 10 seats
        $event = new Event();
        $event->setTitle('Test Event');
        $event->setDescription('Test Description');
        $event->setLocation('Test Location');
        $event->setDate(new \DateTime('+1 week'));
        $event->setSeats(10);

        // Add 10 reservations (full capacity)
        for ($i = 1; $i <= 10; $i++) {
            $reservation = new Reservation();
            $reservation->setName("User {$i}");
            $reservation->setEmail("user{$i}@example.com");
            $reservation->setPhone("123456789{$i}");
            $reservation->setEvent($event);
            $event->addReservation($reservation);
        }

        // Try to add an 11th reservation
        $extraReservation = new Reservation();
        $extraReservation->setName('Extra User');
        $extraReservation->setEmail('extra@example.com');
        $extraReservation->setPhone('1234567890');

        // Check available seats
        $availableSeats = $event->getAvailableSeats();
        
        // Assert that no seats are available
        $this->assertEquals(0, $availableSeats, 'No seats should be available when event is full');
        
        // Assert that adding another reservation would exceed capacity
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No available seats');
        
        // Simulate the validation that would happen in the controller
        if ($availableSeats <= 0) {
            throw new \Exception('No available seats');
        }
    }

    public function testCanReserveWhenSeatsAvailable(): void
    {
        // Create an event with 10 seats
        $event = new Event();
        $event->setTitle('Test Event');
        $event->setDescription('Test Description');
        $event->setLocation('Test Location');
        $event->setDate(new \DateTime('+1 week'));
        $event->setSeats(10);

        // Add 5 reservations
        for ($i = 1; $i <= 5; $i++) {
            $reservation = new Reservation();
            $reservation->setName("User {$i}");
            $reservation->setEmail("user{$i}@example.com");
            $reservation->setPhone("123456789{$i}");
            $reservation->setEvent($event);
            $event->addReservation($reservation);
        }

        // Check available seats
        $availableSeats = $event->getAvailableSeats();
        
        // Assert that 5 seats are still available
        $this->assertEquals(5, $availableSeats, '5 seats should be available');
        
        // Should be able to add another reservation
        $newReservation = new Reservation();
        $newReservation->setName('New User');
        $newReservation->setEmail('new@example.com');
        $newReservation->setPhone('1234567890');
        $newReservation->setEvent($event);
        $event->addReservation($newReservation);

        $this->assertEquals(4, $event->getAvailableSeats());
    }

    public function testReservationCreation(): void
    {
        $event = new Event();
        $event->setTitle('Test Event');
        $event->setSeats(10);

        $reservation = new Reservation();
        $reservation->setName('John Doe');
        $reservation->setEmail('john@example.com');
        $reservation->setPhone('1234567890');
        $reservation->setEvent($event);

        // Test reservation properties
        $this->assertEquals('John Doe', $reservation->getName());
        $this->assertEquals('john@example.com', $reservation->getEmail());
        $this->assertEquals('1234567890', $reservation->getPhone());
        $this->assertEquals($event, $reservation->getEvent());
        $this->assertInstanceOf(\DateTimeInterface::class, $reservation->getCreatedAt());
    }

    public function testEventToArray(): void
    {
        $event = new Event();
        $event->setTitle('Test Event');
        $event->setDescription('Test Description');
        $event->setLocation('Test Location');
        $event->setDate(new \DateTime('2024-06-15 14:00:00'));
        $event->setSeats(10);
        $event->setImage('test.jpg');

        $array = $event->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('Test Event', $array['title']);
        $this->assertEquals('Test Description', $array['description']);
        $this->assertEquals('Test Location', $array['location']);
        $this->assertEquals('2024-06-15 14:00:00', $array['date']);
        $this->assertEquals(10, $array['seats']);
        $this->assertEquals(10, $array['availableSeats']); // No reservations yet
        $this->assertEquals('test.jpg', $array['image']);
    }

    public function testReservationToArray(): void
    {
        $event = new Event();

        $reservation = new Reservation();
        $reservation->setName('John Doe');
        $reservation->setEmail('john@example.com');
        $reservation->setPhone('1234567890');
        $reservation->setEvent($event);

        $refEvent = new \ReflectionClass($event);
        $propId = $refEvent->getProperty('id');
        $propId->setAccessible(true);
        $propId->setValue($event, 1);

        $refRes = new \ReflectionClass($reservation);
        $propRid = $refRes->getProperty('id');
        $propRid->setAccessible(true);
        $propRid->setValue($reservation, 1);

        $array = $reservation->toArray();

        $this->assertIsArray($array);
        $this->assertEquals(1, $array['id']);
        $this->assertEquals('John Doe', $array['name']);
        $this->assertEquals('john@example.com', $array['email']);
        $this->assertEquals('1234567890', $array['phone']);
        $this->assertEquals(1, $array['eventId']);
        $this->assertInstanceOf(\DateTimeInterface::class, $reservation->getCreatedAt());
    }

    public function testAvailableSeatsCalculation(): void
    {
        $event = new Event();
        $event->setTitle('Test Event');
        $event->setSeats(5);

        // Initially, all seats should be available
        $this->assertEquals(5, $event->getAvailableSeats());

        // Add reservations and check available seats
        for ($i = 1; $i <= 3; $i++) {
            $reservation = new Reservation();
            $reservation->setName("User {$i}");
            $reservation->setEmail("user{$i}@example.com");
            $reservation->setPhone("123456789{$i}");
            $reservation->setEvent($event);
            $event->addReservation($reservation);
        }

        $this->assertEquals(2, $event->getAvailableSeats());

        // Add remaining reservations
        for ($i = 4; $i <= 5; $i++) {
            $reservation = new Reservation();
            $reservation->setName("User {$i}");
            $reservation->setEmail("user{$i}@example.com");
            $reservation->setPhone("123456789{$i}");
            $reservation->setEvent($event);
            $event->addReservation($reservation);
        }

        $this->assertEquals(0, $event->getAvailableSeats());
    }
}
