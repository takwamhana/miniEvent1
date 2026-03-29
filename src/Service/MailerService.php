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
        private string $mailerReplyTo,
    ) {
    }

    public function sendReservationConfirmation(Reservation $reservation): void
    {
        $event = $reservation->getEvent();

        $email = (new Email())
            ->from($this->mailerFrom)
            ->replyTo($this->mailerReplyTo)
            ->to($reservation->getEmail())
            ->subject('🎉 Your Reservation is Confirmed - ' . $event->getTitle())
            ->html($this->generateConfirmationHtml($reservation));

        $this->mailer->send($email);
    }

    private function generateConfirmationHtml(Reservation $reservation): string
    {
        $event = $reservation->getEvent();
        $eventDate = $event->getDate();
        $formattedDate = $eventDate->format('F d, Y');
        $formattedTime = $eventDate->format('h:i A');

        return "<!DOCTYPE html>
<html lang='en' style='margin: 0; padding: 0;'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
            background: linear-gradient(135deg, #f8fafb 0%, #f1f5f9 100%);
            color: #0f172a;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(15, 23, 42, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .logo-section {
            margin-bottom: 20px;
        }
        .logo-icon {
            display: inline-block;
            width: 50px;
            height: 50px;
            background: rgba(200, 168, 130, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, #c8a882 0%, #e8c4b0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .header p {
            margin: 8px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            margin: 0 0 30px 0;
            color: #0f172a;
        }
        .confirmation-badge {
            background: #f0fdf4;
            border-left: 4px solid #10b981;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .badge-icon {
            font-size: 24px;
        }
        .badge-text {
            margin: 0;
            color: #166534;
            font-weight: 500;
        }
        .event-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
        }
        .event-title {
            margin: 0 0 20px 0;
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
        }
        .event-details {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .detail-row {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }
        .detail-icon {
            font-size: 20px;
            min-width: 30px;
            text-align: center;
        }
        .detail-content {
            flex: 1;
        }
        .detail-label {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 4px 0;
        }
        .detail-value {
            margin: 0;
            font-size: 16px;
            color: #0f172a;
            font-weight: 500;
        }
        .reservation-details {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .section-title {
            margin: 0 0 15px 0;
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .detail-item {
            padding: 12px;
            background: #f8fafc;
            border-radius: 8px;
        }
        .detail-item-label {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 5px 0;
        }
        .detail-item-value {
            margin: 0;
            font-size: 14px;
            color: #0f172a;
            font-weight: 500;
            word-break: break-word;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: white;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            margin: 30px 0;
            transition: all 0.3s ease;
        }
        .cta-button:hover {
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.2);
            transform: translateY(-2px);
        }
        .info-section {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .info-icon {
            font-size: 20px;
            margin-bottom: 5px;
        }
        .info-text {
            margin: 0;
            font-size: 14px;
            color: #92400e;
            line-height: 1.6;
        }
        .footer {
            background: #0f172a;
            color: rgba(255, 255, 255, 0.7);
            padding: 30px;
            text-align: center;
            font-size: 13px;
        }
        .footer-brand {
            margin-bottom: 15px;
            font-size: 16px;
            font-weight: 700;
            background: linear-gradient(135deg, #c8a882 0%, #e8c4b0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .footer-links {
            margin: 15px 0 0 0;
            padding: 0;
        }
        .footer-link {
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            margin: 0 10px;
            display: inline-block;
        }
        .footer-link:hover {
            color: #c8a882;
        }
        .divider {
            height: 1px;
            background: rgba(200, 168, 130, 0.2);
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class='email-container'>
        <!-- Header -->
        <div class='header'>
            <div class='logo-section'>
                <div class='logo-icon'>🎫</div>
            </div>
            <h1>Eventize</h1>
            <p>Event Management Platform</p>
        </div>

        <!-- Content -->
        <div class='content'>
            <!-- Greeting -->
            <p class='greeting'>Hello <strong>{$reservation->getName()}</strong>,</p>

            <!-- Confirmation Badge -->
            <div class='confirmation-badge'>
                <div class='badge-icon'>✅</div>
                <p class='badge-text'>Your reservation has been confirmed!</p>
            </div>

            <!-- Event Details -->
            <div class='event-section'>
                <h2 class='event-title'>{$event->getTitle()}</h2>

                <div class='event-details'>
                    <div class='detail-row'>
                        <div class='detail-icon'>📅</div>
                        <div class='detail-content'>
                            <p class='detail-label'>Date</p>
                            <p class='detail-value'>{$formattedDate}</p>
                        </div>
                    </div>

                    <div class='detail-row'>
                        <div class='detail-icon'>🕐</div>
                        <div class='detail-content'>
                            <p class='detail-label'>Time</p>
                            <p class='detail-value'>{$formattedTime}</p>
                        </div>
                    </div>

                    <div class='detail-row'>
                        <div class='detail-icon'>📍</div>
                        <div class='detail-content'>
                            <p class='detail-label'>Location</p>
                            <p class='detail-value'>{$event->getLocation()}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reservation Details -->
            <div class='reservation-details'>
                <p class='section-title'>Reservation Details</p>
                <div class='detail-grid'>
                    <div class='detail-item'>
                        <p class='detail-item-label'>Name</p>
                        <p class='detail-item-value'>{$reservation->getName()}</p>
                    </div>
                    <div class='detail-item'>
                        <p class='detail-item-label'>Email</p>
                        <p class='detail-item-value'>{$reservation->getEmail()}</p>
                    </div>
                    <div class='detail-item'>
                        <p class='detail-item-label'>Phone</p>
                        <p class='detail-item-value'>{$reservation->getPhone()}</p>
                    </div>
                    <div class='detail-item'>
                        <p class='detail-item-label'>Confirmation #</p>
                        <p class='detail-item-value'>#{$reservation->getId()}</p>
                    </div>
                </div>
            </div>

            <!-- Info Section -->
            <div class='info-section'>
                <div class='info-icon'>ℹ️</div>
                <p class='info-text'><strong>Save this email</strong> for your records. You may need to show this confirmation when you arrive at the event. If you need to cancel or modify your reservation, please contact us as soon as possible.</p>
            </div>

            <!-- Description (if available) -->
            " . ($event->getDescription() ? "
            <div class='event-section'>
                <p class='section-title'>About This Event</p>
                <p style='margin: 0; color: #64748b; line-height: 1.7;'>{$event->getDescription()}</p>
            </div>
            " : "") . "

            <!-- CTA Button -->
            <center>
                <a href='http://localhost:8080/event.html?id={$event->getId()}' class='cta-button'>View Event Details 🎯</a>
            </center>
        </div>

        <!-- Footer -->
        <div class='footer'>
            <div class='footer-brand'>Eventize</div>
            <p style='margin: 8px 0; line-height: 1.6;'>Premium Event Management Platform</p>
            <div class='divider'></div>
            <p style='margin: 12px 0; font-size: 12px;'>
                📧 Email: noreply@eventize.local<br>
                🌐 Website: <a href='http://localhost:8080' style='color: #c8a882; text-decoration: none;'>www.eventize.local</a>
            </p>
            <div class='divider'></div>
            <p style='margin: 12px 0 0 0;'>© 2026 Eventize. All rights reserved.</p>
            <div class='footer-links'>
                <a href='#' class='footer-link'>Privacy Policy</a>
                <a href='#' class='footer-link'>Terms of Service</a>
                <a href='#' class='footer-link'>Contact Us</a>
            </div>
        </div>
    </div>
</body>
</html>";
    }
}
