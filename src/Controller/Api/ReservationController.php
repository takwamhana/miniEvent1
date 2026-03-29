<?php

namespace App\Controller\Api;

use App\Entity\Event;
use App\Entity\Reservation;
use App\Repository\EventRepository;
use App\Repository\ReservationRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
class ReservationController extends AbstractController
{
    private EventRepository $eventRepository;
    private ReservationRepository $reservationRepository;
    private EntityManagerInterface $entityManager;
    private MailerService $mailerService;

    public function __construct(
        EventRepository $eventRepository,
        ReservationRepository $reservationRepository,
        EntityManagerInterface $entityManager,
        MailerService $mailerService
    ) {
        $this->eventRepository = $eventRepository;
        $this->reservationRepository = $reservationRepository;
        $this->entityManager = $entityManager;
        $this->mailerService = $mailerService;
    }

    #[Route('/events/{eventId}/reservations', name: 'api_reservation_create', methods: ['POST'])]
    public function create(int $eventId, Request $request): JsonResponse
    {
        $event = $this->eventRepository->find($eventId);
        
        if (!$event) {
            return new JsonResponse(['error' => 'Event not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true) ?? [];

        // Check if there are available seats
        $currentReservations = count($event->getReservations());
        if ($currentReservations >= $event->getSeats()) {
            return new JsonResponse(['error' => 'No available seats'], Response::HTTP_BAD_REQUEST);
        }

        $reservation = new Reservation();
        $reservation->setName($data['name'] ?? '');
        $reservation->setEmail($data['email'] ?? '');
        $reservation->setPhone($data['phone'] ?? '');
        $reservation->setEvent($event);

        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        // Send confirmation email
        try {
            $this->mailerService->sendReservationConfirmation($reservation);
        } catch (\Exception $e) {
            // Log error but don't fail the reservation
            error_log('Failed to send confirmation email: ' . $e->getMessage());
        }

        return new JsonResponse($reservation->toArray(), Response::HTTP_CREATED);
    }

    #[Route('/admin/events/{eventId}/reservations', name: 'api_admin_reservations_list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function listByEvent(int $eventId): JsonResponse
    {
        $event = $this->eventRepository->find($eventId);
        
        if (!$event) {
            return new JsonResponse(['error' => 'Event not found'], Response::HTTP_NOT_FOUND);
        }

        $reservations = $this->reservationRepository->findBy(['event' => $event]);
        $data = array_map(function($reservation) {
            return $reservation->toArray();
        }, $reservations);
        
        return new JsonResponse($data);
    }

    #[Route('/admin/reservations/{id}', name: 'api_admin_reservation_update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function updateReservation(int $id, Request $request): JsonResponse
    {
        $reservation = $this->reservationRepository->find($id);
        
        if (!$reservation) {
            return new JsonResponse(['error' => 'Reservation not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        // Update reservation fields
        if (isset($data['name'])) {
            $reservation->setName($data['name']);
        }
        if (isset($data['email'])) {
            $reservation->setEmail($data['email']);
        }
        if (isset($data['phone'])) {
            $reservation->setPhone($data['phone']);
        }

        $this->entityManager->flush();

        return new JsonResponse($reservation->toArray());
    }

    #[Route('/admin/reservations/{id}', name: 'api_admin_reservation_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteReservation(int $id): JsonResponse
    {
        $reservation = $this->reservationRepository->find($id);
        
        if (!$reservation) {
            return new JsonResponse(['error' => 'Reservation not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($reservation);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Reservation deleted successfully']);
    }
}
