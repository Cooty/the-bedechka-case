<?php

namespace App\Controller;

use App\Factory\CacheFactory;
use App\Repository\NewsRepository;
use App\Service\JSONAPIService;
use DateInterval;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * @Route("/api")
 */
class NewsController extends AbstractController
{
    /**
     * @var NewsRepository
     */
    private $newsRepository;

    /**
     * @var JSONAPIService
     */
    private $jsonAPI;

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var CacheFactory
     */
    private $cacheFactory;

    public function __construct(
        NewsRepository $newsRepository,
        JSONAPIService $jsonAPI,
        LoggerInterface $logger,
        CacheFactory $cacheFactory
    )
    {
        $this->newsRepository = $newsRepository;
        $this->jsonAPI = $jsonAPI;
        $this->logger = $logger;
        $this->cacheFactory = $cacheFactory;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/news", methods={"GET"}, name="api_news_items")
     */
    public function getNewsItems(Request $request): JsonResponse
    {
        $submittedToken = $request->query->get('token');

        if(!$this->isCsrfTokenValid('news', $submittedToken)) {
            return $this->jsonAPI->makeHTTPJSONResponse(Response::HTTP_UNAUTHORIZED);
        }

        try {
            $pageSize = (int)$request->query->get('pageSize');
            $offset = (int)$request->query->get('offset');

            $cache = $this->cacheFactory->create();
            $cacheKey = 'news-'.$pageSize.$offset;
            $newsItems = $cache->get($cacheKey, function(ItemInterface $item) use ($pageSize, $offset) {
                $item->expiresAfter(DateInterval::createFromDateString('5 hours'));

                $news = $this->newsRepository->findActiveByPage($pageSize, $offset);

                return $news;
            });

            return $this->json(['items'=> $newsItems]);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage().' '.$exception->getTraceAsString());
            return $this->jsonAPI->makeHTTPJSONResponse(Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (InvalidArgumentException $exception) {
            $this->logger->error($exception->getMessage().' '.$exception->getTraceAsString());
            return $this->jsonAPI->makeHTTPJSONResponse(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
}