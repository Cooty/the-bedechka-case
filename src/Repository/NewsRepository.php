<?php

namespace App\Repository;

use App\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Enum\Pagination;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Psr\Log\LoggerInterface;

/**
 * @method News|null find($id, $lockMode = null, $lockVersion = null)
 * @method News|null findOneBy(array $criteria, array $orderBy = null)
 * @method News[]    findAll()
 * @method News[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsRepository extends ServiceEntityRepository
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        parent::__construct($registry, News::class);
        $this->logger = $logger;
    }

    /**
     * @return News[] Returns an array of News objects
     */
    public function findActive(): array
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
    public function findActiveByPage(int $pageSize = Pagination::NEWS_PAGE_SIZE, int $offset = 0): array
    {
        return $this->createQueryBuilder('n')
            ->select('n')
            ->where('n.archived = false')
            ->setFirstResult($offset)
            ->setMaxResults($pageSize)
            ->orderBy('n.publishingDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return int
     */
    public function getItemCount(): int
    {
        try {
            return $this->createQueryBuilder('n')
                ->select('count(n.id)')
                ->where('n.archived = false')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException | NonUniqueResultException $e) {
            $this->logger->error($e->getMessage().' '.$e->getTraceAsString());
            return 0;
        }
    }
}
