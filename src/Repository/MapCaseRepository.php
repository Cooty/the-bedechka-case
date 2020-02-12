<?php

namespace App\Repository;

use App\Entity\MapCase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method MapCase|null find($id, $lockMode = null, $lockVersion = null)
 * @method MapCase|null findOneBy(array $criteria, array $orderBy = null)
 * @method MapCase[]    findAll()
 * @method MapCase[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MapCaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MapCase::class);
    }

    /**
      * @return MapCase[] Returns an array of MapCase objects
    */
    public function findActive()
    {
        return $this->createQueryBuilder('cases')
            ->select('cases')
            ->where('cases.archived = false')
            ->orderBy('cases.createdAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?MapCase
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
