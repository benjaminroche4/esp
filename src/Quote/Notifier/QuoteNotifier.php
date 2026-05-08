<?php

declare(strict_types=1);

namespace App\Quote\Notifier;

use App\Quote\Entity\QuoteRequest;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

final readonly class QuoteNotifier
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

    public function notify(QuoteRequest $quote): void
    {
        $service = $quote->getServiceType() ?? 'service';
        $city = $quote->getCity() ?? '';
        $fullName = trim($quote->getFirstName() . ' ' . $quote->getLastName());
        $customerEmail = (string) $quote->getEmail();

        $admin = new TemplatedEmail()
            ->from(new Address($this->mailerFrom, 'ESP Site internet'))
            ->to(new Address($this->contactEmail))
            ->replyTo(new Address($customerEmail, $fullName))
            ->subject(sprintf('[ESP] Nouveau devis – %s – %s', ucfirst($service), $city))
            ->htmlTemplate('emails/quote_admin.html.twig')
            ->context(['quote' => $quote]);

        try {
            $this->mailer->send($admin);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Quote admin email failed to send', [
                'exception' => $e,
                'quote_id' => $quote->getId(),
            ]);
        }

        $customer = new TemplatedEmail()
            ->from(new Address($this->mailerFrom, 'ESP Débarras et Nettoyage'))
            ->to(new Address($customerEmail, $fullName))
            ->subject('Votre demande de devis ESP – confirmation')
            ->htmlTemplate('emails/quote_customer.html.twig')
            ->context(['quote' => $quote]);

        try {
            $this->mailer->send($customer);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Quote customer email failed to send', [
                'exception' => $e,
                'quote_id' => $quote->getId(),
            ]);
        }
    }
}
