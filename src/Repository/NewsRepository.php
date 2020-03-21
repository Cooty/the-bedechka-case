<?php

namespace App\Repository;

use App\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Enum\Pagination;

/**
 * @method News|null find($id, $lockMode = null, $lockVersion = null)
 * @method News|null findOneBy(array $criteria, array $orderBy = null)
 * @method News[]    findAll()
 * @method News[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    /**
     * @return News[] Returns an array of News objects
     */
    public function findActive()
    {
        return $this->createQueryBuilder('n')
            ->select('n')
            ->where('n.archived = false')
            ->orderBy('n.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $pageSize
     * @param int $offset
     * @return News[] Returns an array of News objects
     */
    public function findActiveByPage(int $pageSize = Pagination::NEWS_PAGE_SIZE, int $offset = 0)
    {
        return $this->createQueryBuilder('n')
            ->select('n')
            ->where('n.archived = false')
            ->setFirstResult($offset)
            ->setMaxResults($pageSize)
            ->orderBy('n.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?News
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
