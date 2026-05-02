<?php

declare(strict_types=1);

namespace App\Contact\Notifier;

use App\Contact\Entity\Contact;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

final readonly class ContactNotifier
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger,
        #[Autowire('%env(CONTACT_EMAIL)%')]
        private string $contactEmail,
        #[Autowire('%env(MAILER_FROM)%')]
        private string $mailerFrom,
    ) {
    }

    public function notify(Contact $contact): void
    {
        $email = new TemplatedEmail()
            ->from(new Address($this->mailerFrom, 'ESP Site internet'))
            ->to(new Address($this->contactEmail))
            ->replyTo(new Address((string) $contact->getEmail(), trim($contact->getFirstName() . ' ' . $contact->getLastName())))
            ->subject('[ESP Site internet] Nouvelle demande de contact')
            ->htmlTemplate('emails/contact.html.twig')
            ->context(['contact' => $contact]);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Contact email failed to send', [
                'exception' => $e,
                'contact_id' => $contact->getId(),
            ]);
        }
    }
}
