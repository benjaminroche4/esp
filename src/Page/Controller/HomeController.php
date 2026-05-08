<?php

declare(strict_types=1);

namespace App\Page\Controller;

use App\Quote\Entity\QuoteRequest;
use App\Quote\Form\Flow\QuoteFlowType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'], options: ['sitemap' => ['priority' => 1]])]
    public function index(): Response
    {
        $flow = $this->createForm(QuoteFlowType::class, new QuoteRequest());

        return $this->render('public/home/index.html.twig', [
            'quoteForm' => $flow->getStepForm(),
            'quoteFlow' => $flow,
        ]);
    }
}
