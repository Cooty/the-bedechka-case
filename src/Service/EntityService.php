<?php

namespace App\Service;

use App\Entity\MapCase;
use App\Entity\News;
use App\Repository\MapCaseRepository;
use App\Repository\NewsRepository;
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

    /**
     * @var NewsRepository
     */
    private $newsRepository;

    public function __construct(
        MapCaseRepository $mapCaseRepository,
        NewsRepository $newsRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->mapCaseRepository = $mapCaseRepository;
        $this->entityManager = $entityManager;
        $this->newsRepository = $newsRepository;
    }

    /**
     * @param string $entityName
     * @param string $id
     * @return object|null
     */
    public function getEntityById(string $entityName, string $id)
    {
        switch ($entityName) {
            case MapCase::URL_PARAM_NAME:
                return $this->mapCaseRepository->find($id);
            case News::URL_PARAM_NAME:
                return $this->newsRepository->find($id);
            default:
                return null;
        }
    }
}