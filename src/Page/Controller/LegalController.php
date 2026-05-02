<?php

declare(strict_types=1);

namespace App\Page\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;

final class LegalController extends AbstractController
{
    #[Route('/mentions-legales', name: 'app_legal_notice', methods: ['GET'], options: ['sitemap' => ['priority' => 0.3]])]
    #[Cache(public: true, maxage: 86400, smaxage: 86400)]
    public function legalNotice(): Response
    {
        return $this->render('public/legal/legal_notice.html.twig');
    }

    #[Route('/donnees-personnelles', name: 'app_personal_data', methods: ['GET'], options: ['sitemap' => ['priority' => 0.3]])]
    #[Cache(public: true, maxage: 86400, smaxage: 86400)]
    public function personalData(): Response
    {
        return $this->render('public/legal/personal_data.html.twig');
    }
}
