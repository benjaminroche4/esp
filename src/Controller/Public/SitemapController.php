<?php

namespace App\Controller\Public;

use App\Repository\BlogPostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SitemapController extends AbstractController{
    public function __construct(
        private readonly BlogPostRepository $blogPostRepository,
    ){
    }
    #[Route('/sitemap', name: 'app_sitemap')]
    public function index(): Response
    {
        $posts = $this->blogPostRepository->findAllByOrderDesc();

        return $this->render('public/sitemap/index.html.twig',[
            'posts' => $posts,
        ]);
    }
}
