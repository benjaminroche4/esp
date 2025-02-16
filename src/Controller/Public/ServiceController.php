<?php

namespace App\Controller\Public;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ServiceController extends AbstractController{
    #[Route('/nos-interventions', name: 'app_service_intervention', options: ['sitemap' => ['priority' => 0.8]])]
    public function intervention(): Response
    {
        return $this->render('public/service/intervention.html.twig');
    }
}
