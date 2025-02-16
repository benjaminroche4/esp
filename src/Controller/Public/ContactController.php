<?php

namespace App\Controller\Public;

use App\Config\EmailConfig;
use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact', options: ['sitemap' => ['priority' => 0.8]])]
    public function index(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer, LoggerInterface $logger): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $contact->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($contact);
            $entityManager->flush();

            $emailContact = (new TemplatedEmail())
                ->from($contact->getEmail())
                ->to(new Address(EmailConfig::CONTACT_EMAIL))
                ->subject('[ESP Site internet] Nouvelle demande de contact')
                ->htmlTemplate('emails/contact.html.twig')
                ->context([
                    'createdAt' => new \DateTimeImmutable(),
                    'contactType' => $contact->getContactType(),
                    'firstName' => $contact->getFirstName(),
                    'lastName' => $contact->getLastName(),
                    'city' => $contact->getCity(),
                    'zipCode' => $contact->getZipCode(),
                    'emailContact' => $contact->getEmail(),
                    'phoneNumber' => $contact->getPhoneNumber(),
                    'message' => $contact->getMessage(),
                ]);

            try {
                $mailer->send($emailContact);
            } catch (TransportExceptionInterface $e) {
                $logger->error('An error has been throw during the send :'. $e->getMessage());
            }

            $this->addFlash('success', 'Votre message a bien été envoyé. Nous reviendrons vers vous dans un délai maximal de 24 heures.');
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('public/contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
