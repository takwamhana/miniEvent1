<?php

namespace App\Controller\Api;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/events')]
class EventController extends AbstractController
{
    private EventRepository $eventRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(EventRepository $eventRepository, EntityManagerInterface $entityManager)
    {
        $this->eventRepository = $eventRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('', name: 'api_events_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $events = $this->eventRepository->findAll();
        $data = array_map(fn($event) => $event->toArray(), $events);
        
        return new JsonResponse($data);
    }

    #[Route('/{id}', name: 'api_event_show', methods: ['GET'])]
    public function show(Event $event): JsonResponse
    {
        return new JsonResponse($event->toArray());
    }

    #[Route('', name: 'api_events_create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $event = new Event();
        $event->setTitle($data['title'] ?? 'Untitled Event');
        $event->setDescription($data['description'] ?? '');
        $event->setDate(new \DateTime($data['date'] ?? 'now'));
        $event->setLocation($data['location'] ?? '');
        $event->setSeats($data['seats'] ?? 0);
        $event->setImage($data['image'] ?? '');

        $entityManager->persist($event);
        $entityManager->flush();

        return new JsonResponse($event->toArray(), Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_event_update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(Event $event, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['title'])) {
            $event->setTitle($data['title']);
        }
        if (isset($data['description'])) {
            $event->setDescription($data['description']);
        }
        if (isset($data['location'])) {
            $event->setLocation($data['location']);
        }
        if (isset($data['seats'])) {
            $event->setSeats($data['seats']);
        }
        if (isset($data['image'])) {
            $event->setImage($data['image']);
        }
        if (isset($data['date'])) {
            $event->setDate(new \DateTime($data['date']));
        }

        $this->entityManager->flush();

        return new JsonResponse($event->toArray());
    }

    #[Route('/{id}', name: 'api_event_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Event $event): JsonResponse
    {
        $this->entityManager->remove($event);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
