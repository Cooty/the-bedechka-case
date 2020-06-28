<?php

namespace App\Repository;

use App\Entity\CrewMember;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CrewMember|null find($id, $lockMode = null, $lockVersion = null)
 * @method CrewMember|null findOneBy(array $criteria, array $orderBy = null)
 * @method CrewMember[]    findAll()
 * @method CrewMember[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CrewMemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CrewMember::class);
    }

    /**
     * @return CrewMember[] Returns an array of CrewMember objects
     */
    public function findActive()
    {
        return $this->createQueryBuilder('cm')
            ->select('cm')
            ->where('cm.archived = false')
            ->orderBy('cm.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return CrewMemeber[] Returns an array of CrewMemeber objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CrewMemeber
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
