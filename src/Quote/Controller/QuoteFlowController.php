<?php

declare(strict_types=1);

namespace App\Quote\Controller;

use App\Quote\Entity\QuoteRequest;
use App\Quote\Form\Flow\QuoteFlowType;
use App\Quote\Notifier\QuoteNotifier;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;
use Symfony\Component\Routing\Attribute\Route;

final class QuoteFlowController extends AbstractController
{
    #[Route('/devis', name: 'app_quote_flow', methods: ['GET', 'POST'])]
    public function flow(
        Request $request,
        EntityManagerInterface $entityManager,
        QuoteNotifier $notifier,
        LoggerInterface $logger,
        #[Target('quoteFlowLimiter')]
        RateLimiterFactoryInterface $quoteFlowLimiter,
    ): Response {
        $flow = $this->createForm(QuoteFlowType::class, new QuoteRequest());
        $flow->handleRequest($request);

        if ($flow->isSubmitted() && $flow->isValid()) {
            if (!$flow->isFinished()) {
                // Step transition (Next/Previous): trigger button handler so cursor
                // advances and is saved in SessionDataStorage; PRG-redirect so the
                // browser does a clean GET to render the new step.
                $flow->getStepForm();

                return $this->redirectToRoute('app_quote_flow');
            }

            /** @var QuoteRequest $quote */
            $quote = $flow->getData();
            $quote->setCreatedAt(new \DateTimeImmutable());

            $contactStep = $flow->get('contact');
            if ($contactStep->has('website') && $contactStep->get('website')->getData()) {
                return $this->redirectToRoute('app_quote_success');
            }

            // Rate limiter désactivé pour les tests — réactiver avant prod.
            // $consume = $quoteFlowLimiter->create($request->getClientIp() ?? 'anon')->consume();
            // if (!$consume->isAccepted()) {
            //     $this->addFlash('error', 'Trop de tentatives. Réessayez dans quelques minutes.');
            //     return $this->redirectToRoute('app_home');
            // }

            try {
                $entityManager->persist($quote);
                $entityManager->flush();
            } catch (\Throwable $e) {
                $logger->error('Quote flow persist failed', ['exception' => $e]);
                $this->addFlash('error', "Une erreur est survenue lors de l'envoi. Réessayez dans quelques instants ou contactez-nous au +33 6 41 28 88 48.");

                return $this->render('public/home/index.html.twig', [
                    'quoteForm' => $flow->getStepForm(),
                    'quoteFlow' => $flow,
                ]);
            }

            $notifier->notify($quote);

            return $this->redirectToRoute('app_quote_success');
        }

        $status = ($request->isMethod('POST') && $flow->isSubmitted())
            ? Response::HTTP_UNPROCESSABLE_ENTITY
            : Response::HTTP_OK;

        return $this->render('public/home/index.html.twig', [
            'quoteForm' => $flow->getStepForm(),
            'quoteFlow' => $flow,
        ], new Response(status: $status));
    }

    #[Route('/devis/merci', name: 'app_quote_success', methods: ['GET'])]
    #[Cache(public: false, maxage: 0)]
    public function success(): Response
    {
        return $this->render('public/quote/success.html.twig');
    }
}
