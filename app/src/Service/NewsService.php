<?php

namespace App\Service;

use App\Repository\NewsRepository;
use App\Enum\Pagination;
use App\Entity\News;

class NewsService
{
    /**
     * @var NewsRepository
     */
    private $newsRepository;

    /**
     * @var TransportService
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

    public function hasPagination(): bool
    {
        $secondPage = $this->newsRepository->findActiveByPage(Pagination::NEWS_PAGE_SIZE,
            Pagination::NEWS_PAGE_SIZE);

        return (bool)count($secondPage);
    }
}