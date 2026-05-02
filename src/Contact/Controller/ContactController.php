<?php

declare(strict_types=1);

namespace App\Contact\Controller;

use App\Contact\Entity\Contact;
use App\Contact\Form\ContactType;
use App\Contact\Notifier\ContactNotifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'], options: ['sitemap' => ['priority' => 0.8]])]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        ContactNotifier $notifier,
    ): Response {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($contact);
            $entityManager->flush();

            $notifier->notify($contact);

            $this->addFlash('success', 'Votre message a bien été envoyé. Nous reviendrons vers vous dans un délai maximal de 24 heures.');

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('public/contact/index.html.twig', [
            'form' => $form,
        ]);
    }
}
