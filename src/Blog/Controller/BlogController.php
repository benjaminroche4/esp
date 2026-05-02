<?php

declare(strict_types=1);

namespace App\Blog\Controller;

use App\Blog\Repository\BlogPostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;

final class BlogController extends AbstractController
{
    public function __construct(
        private readonly BlogPostRepository $blogPostRepository,
    ) {
    }

    #[Route('/blog', name: 'app_blog', methods: ['GET'], options: ['sitemap' => ['priority' => 0.8, 'section' => 'blog']])]
    #[Cache(public: true, maxage: 600, smaxage: 600)]
    public function blogList(): Response
    {
        return $this->render('public/blog/blog_list.html.twig', [
            'posts' => $this->blogPostRepository->findAllPublishedDesc(),
        ]);
    }

    #[Route('/blog/{slug}', name: 'app_blog_post', methods: ['GET'], requirements: ['slug' => '[a-z0-9-]+'])]
    #[Cache(public: true, maxage: 600, smaxage: 600)]
    public function blogPost(string $slug): Response
    {
        $post = $this->blogPostRepository->findPublishedBySlug($slug)
            ?? throw $this->createNotFoundException();

        return $this->render('public/blog/blog_post.html.twig', [
            'post' => $post,
        ]);
    }
}
