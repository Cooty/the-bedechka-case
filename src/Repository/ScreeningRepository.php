<?php

namespace App\Repository;

use App\Entity\Screening;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Screening|null find($id, $lockMode = null, $lockVersion = null)
 * @method Screening|null findOneBy(array $criteria, array $orderBy = null)
 * @method Screening[]    findAll()
 * @method Screening[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScreeningRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Screening::class);
    }

    /**
     * @return Screening[] Returns an array of Screening objects
    */
    public function findActive()
    {
        return $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.archived = false')
            ->orderBy('s.start', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
