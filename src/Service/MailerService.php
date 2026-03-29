<?php

namespace App\Service;

use App\Entity\Reservation;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    public function __construct(
        private MailerInterface $mailer,
        private string $mailerFrom,
    ) {
    }

    public function sendReservationConfirmation(Reservation $reservation): void
    {
        $event = $reservation->getEvent();

        $email = (new Email())
            ->from($this->mailerFrom)
            ->to($reservation->getEmail())
            ->subject('Confirmation de réservation - ' . $event->getTitle())
            ->html($this->generateConfirmationHtml($reservation));

        $this->mailer->send($email);
    }

    private function generateConfirmationHtml(Reservation $reservation): string
    {
        $event = $reservation->getEvent();
        
        return "
        <html>
        <body>
            <h2>Confirmation de réservation</h2>
            <p>Bonjour {$reservation->getName()},</p>
            <p>Votre réservation a été confirmée pour l'événement suivant :</p>
            
            <h3>{$event->getTitle()}</h3>
            <ul>
                <li><strong>Date :</strong> {$event->getDate()->format('d/m/Y H:i')}</li>
                <li><strong>Lieu :</strong> {$event->getLocation()}</li>
                <li><strong>Nom du réservant :</strong> {$reservation->getName()}</li>
                <li><strong>Email :</strong> {$reservation->getEmail()}</li>
                <li><strong>Téléphone :</strong> {$reservation->getPhone()}</li>
            </ul>
            
            <p>Merci pour votre réservation !</p>
            <p>Cordialement,<br>L'équipe Mini Event</p>
        </body>
        </html>";
    }
}
