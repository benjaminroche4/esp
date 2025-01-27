<?php

namespace App\Controller\public;

use App\Repository\BlogPostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BlogController extends AbstractController
{
    public function __construct(
        private readonly BlogPostRepository $blogPostRepository
    ){
    }

    #[Route('/blog', name: 'app_blog', options: ['sitemap' => ['priority' => 0.8, 'section' => 'blog']])]
    public function blogList(): Response
    {
        $posts = $this->blogPostRepository->findAllByOrderDesc();

        return $this->render('public/blog/blog_list.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/blog/{slug}', name: 'app_blog_post')]
    public function blogPost(string $slug): Response
    {
        $post = $this->blogPostRepository->findOneBy(['slug' => $slug]);

        if (!$post || !$post->isStatus()) {
            throw $this->createNotFoundException('The blog post does not exist');
        }

        return $this->render('public/blog/blog_post.html.twig', [
            'post' => $post,
        ]);
    }
}
