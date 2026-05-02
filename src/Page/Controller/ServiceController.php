<?php

declare(strict_types=1);

namespace App\Page\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;

final class ServiceController extends AbstractController
{
    #[Route('/nos-interventions', name: 'app_service_intervention', methods: ['GET'], options: ['sitemap' => ['priority' => 0.8]])]
    #[Cache(public: true, maxage: 3600, smaxage: 3600)]
    public function intervention(): Response
    {
        return $this->render('public/service/intervention.html.twig');
    }
}
