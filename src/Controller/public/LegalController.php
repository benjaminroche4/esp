<?php

namespace App\Controller\public;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LegalController extends AbstractController {
    #[Route('/mentions-legales', name: 'app_legal_notice', options: ['sitemap' => ['priority' => 0.3]])]
    public function legalNotice(): Response
    {
        return $this->render('public/legal/legal_notice.html.twig');
    }

    #[Route('/donnees-personnelles', name: 'app_personal_data', options: ['sitemap' => ['priority' => 0.3]])]
    public function personalData(): Response
    {
        return $this->render('public/legal/personal_data.html.twig');
    }
}
