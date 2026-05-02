<?php

declare(strict_types=1);

namespace App\Page\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'], options: ['sitemap' => ['priority' => 1]])]
    #[Cache(public: true, maxage: 3600, smaxage: 3600)]
    public function index(): Response
    {
        return $this->render('public/home/index.html.twig');
    }
}
