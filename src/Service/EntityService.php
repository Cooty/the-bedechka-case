<?php

namespace App\Service;

use App\Entity\MapCase;
use App\Repository\MapCaseRepository;
use Doctrine\ORM\EntityManagerInterface;

class EntityService
{
    /**
     * @var MapCaseRepository
     */
    private $mapCaseRepository;

    /**
     * @var EntityManagerInterface
     */
    public $entityManager;

    public function __construct(
        MapCaseRepository $mapCaseRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->mapCaseRepository = $mapCaseRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $entityName
     * @param string $id
     * @return object|null
     */
    public function getEntity(string $entityName, string $id)
    {
        if($entityName === MapCase::URL_PARAM_NAME) {
            return $this->mapCaseRepository->find($id);
        } else {
            return null;
        }
    }
}