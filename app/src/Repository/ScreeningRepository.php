<?php

namespace App\Repository;

use App\Entity\Screening;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;

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
    public function findActive(): array
    {
        return $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.archived = false')
            ->orderBy('s.start', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Screening[] Returns an array of Screening objects
     */
    public function findCurrent(): array
    {
        try {
            $now = new \DateTime();
        } catch (Exception $exception) {
            return [];
        }

        return $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.archived = false')
            ->andWhere('s.start > :date')
            ->setParameter('date', $now)
            ->orderBy('s.start', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Screening[] Returns an array of Screening objects
     */
    public function findPast(): array
    {
        try {
            $now = new \DateTime();
        } catch (Exception $exception) {
            return [];
        }

        return $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.archived = false')
            ->andWhere('s.start < :date')
            ->setParameter('date', $now)
            ->orderBy('s.start', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
