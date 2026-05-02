<?php

declare(strict_types=1);

namespace App\Page\Controller;

use App\Blog\Repository\BlogPostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;

final class SitemapController extends AbstractController
{
    public function __construct(
        private readonly BlogPostRepository $blogPostRepository,
    ) {
    }

    #[Route('/sitemap', name: 'app_sitemap', methods: ['GET'])]
    #[Cache(public: true, maxage: 3600, smaxage: 3600)]
    public function index(): Response
    {
        return $this->render('public/sitemap/index.html.twig', [
            'posts' => $this->blogPostRepository->findAllPublishedDesc(),
        ]);
    }
}
