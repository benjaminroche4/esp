<?php

declare(strict_types=1);

namespace App\Blog\Repository;

use App\Blog\Entity\BlogPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BlogPost>
 */
class BlogPostRepository extends ServiceEntityRepository
{
    private const int RESULT_CACHE_TTL = 600;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogPost::class);
    }

    /**
     * @return BlogPost[]
     */
    public function findAllPublishedDesc(): array
    {
        return $this->publishedQueryBuilder()
            ->getQuery()
            ->enableResultCache(self::RESULT_CACHE_TTL, 'blog.published.all')
            ->getResult();
    }

    /**
     * @return BlogPost[]
     */
    public function findLastPublished(int $limit = 3): array
    {
        return $this->publishedQueryBuilder()
            ->setMaxResults($limit)
            ->getQuery()
            ->enableResultCache(self::RESULT_CACHE_TTL, 'blog.published.last_'.$limit)
            ->getResult();
    }

    public function findPublishedBySlug(string $slug): ?BlogPost
    {
        return $this->publishedQueryBuilder()
            ->andWhere('b.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->enableResultCache(self::RESULT_CACHE_TTL, 'blog.published.slug.'.$slug)
            ->getOneOrNullResult();
    }

    private function publishedQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.status = :status')
            ->setParameter('status', true)
            ->orderBy('b.createdAt', 'DESC');
    }
}
