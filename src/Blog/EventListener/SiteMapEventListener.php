<?php

declare(strict_types=1);

namespace App\Blog\EventListener;

use App\Blog\Repository\BlogPostRepository;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsEventListener(event: SitemapPopulateEvent::class)]
final readonly class SiteMapEventListener
{
    public function __construct(
        private BlogPostRepository $blogPostRepository,
    ) {
    }

    public function __invoke(SitemapPopulateEvent $event): void
    {
        $urlContainer = $event->getUrlContainer();
        $urlGenerator = $event->getUrlGenerator();

        foreach ($this->blogPostRepository->findAllPublishedDesc() as $post) {
            $url = new UrlConcrete(
                $urlGenerator->generate(
                    'app_blog_post',
                    ['slug' => $post->getSlug()],
                    UrlGeneratorInterface::ABSOLUTE_URL,
                ),
            );
            $url->setChangefreq(UrlConcrete::CHANGEFREQ_MONTHLY);
            $url->setLastmod($post->getUpdatedAt() ?? $post->getCreatedAt());

            $urlContainer->addUrl($url, 'blog');
        }
    }
}
