<?php

namespace App\Controller;

use App\Factory\CacheFactory;
use App\Repository\NewsRepository;
use App\Service\JSONAPIService;
use App\Service\TransportService;
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

    /**
     * @var TransportService
     */
    private $transport;

    public function __construct(
        NewsRepository $newsRepository,
        JSONAPIService $jsonAPI,
        LoggerInterface $logger,
        CacheFactory $cacheFactory,
        TransportService $transport
    )
    {
        $this->newsRepository = $newsRepository;
        $this->jsonAPI = $jsonAPI;
        $this->logger = $logger;
        $this->cacheFactory = $cacheFactory;
        $this->transport = $transport;
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
            $data = $cache->get($cacheKey, function(ItemInterface $item) use ($pageSize, $offset) {
                $item->expiresAfter(DateInterval::createFromDateString('5 hours'));

                $news = $this->newsRepository->findActiveByPage($pageSize, $offset);
                $newsItemsFrontend = $this->transport->makeNewsItemsFrontend($news);
                $count = $this->newsRepository->getItemCount();

                return ['items'=> $newsItemsFrontend, 'total'=> $count];
            });

            return $this->json($data);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage().' '.$exception->getTraceAsString());
            return $this->jsonAPI->makeHTTPJSONResponse(Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (InvalidArgumentException $exception) {
            $this->logger->error($exception->getMessage().' '.$exception->getTraceAsString());
            return $this->jsonAPI->makeHTTPJSONResponse(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
}