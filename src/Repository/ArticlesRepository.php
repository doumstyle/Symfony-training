<?php

namespace App\Repository;

use App\Entity\Articles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Articles>
 */
class ArticlesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Articles::class);
    }

    public function getLastArticles(int $limit): array
    {
        return $this->createQueryBuilder('article')
            ->orderBy('article.id', 'DESC')
            ->setMaxResults(maxResults: $limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByCategory(int $categoryId): array
    {
        return $this->createQueryBuilder('a')
            ->join('a.category', 'c')
            ->andWhere('c.id = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->getQuery()
            ->getResult()
        ;
    }
}
