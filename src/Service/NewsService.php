<?php

namespace App\Service;

use App\Repository\NewsRepository;
use App\Enum\Pagination;
use App\Entity\News;
use App\Service\TransportService;

class NewsService
{
    /**
     * @var NewsRepository
     */
    private $newsRepository;

    /**
     * @var \App\Service\TransportService
     */
    private $transportService;

    public function __construct(
        NewsRepository $newsRepository,
        TransportService $transportService
    )
    {
        $this->newsRepository = $newsRepository;
        $this->transportService = $transportService;
    }

    /**
     * @return News[]
     */
    public function getFirstPage(): array
    {
        $firstPage = $this->newsRepository->findActiveByPage(Pagination::NEWS_PAGE_SIZE, 0);

        return $this->transportService->makeNewsItemsFrontend($firstPage);
    }

    public function hasPagination(): bool
    {
        $secondPage = $this->newsRepository->findActiveByPage(Pagination::NEWS_PAGE_SIZE,
            Pagination::NEWS_PAGE_SIZE);

        return (bool)count($secondPage);
    }
}