<?php

declare(strict_types=1);

namespace App\Page\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;

final class CityController extends AbstractController
{
    #[Route('/agence/geneve', name: 'app_city_geneva', methods: ['GET'], options: ['sitemap' => ['priority' => 0.9]])]
    #[Cache(public: true, maxage: 3600, smaxage: 3600)]
    public function agencyGeneva(): Response
    {
        return $this->render('city/geneva.html.twig');
    }
}
