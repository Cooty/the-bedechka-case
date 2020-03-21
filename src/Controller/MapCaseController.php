<?php

namespace App\Controller;

use App\Entity\Transport\MapCaseFrontend;
use App\Factory\CacheFactory;
use App\Repository\MapCaseRepository;
use App\Service\JSONAPIService;
use App\Service\TransportService;
use DateInterval;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * @Route("/api")
 */
class MapCaseController extends AbstractController
{
    /**
     * @var MapCaseRepository
     */
    private $mapCaseRepository;

    /**
     * @var TransportService
     */
    private $transport;

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
        MapCaseRepository $mapCaseRepository,
        TransportService $transport,
        JSONAPIService $jsonAPI,
        LoggerInterface $logger,
        CacheFactory $cacheFactory
    )
    {
        $this->mapCaseRepository = $mapCaseRepository;
        $this->transport = $transport;
        $this->jsonAPI = $jsonAPI;
        $this->logger = $logger;
        $this->cacheFactory = $cacheFactory;
    }

    /**
     * @param string $locale
     * @return MapCaseFrontend[]
     */
    private function getItems(string $locale): array
    {
        $cases = $this->mapCaseRepository->findActive();
        $frontendCases = $this->transport->makeMapCasesFrontend($cases, $locale);

        return $frontendCases;
    }

    /**
     * @param Request $request
     * @param string $locale
     * @return JsonResponse
     * @Route("/map-cases/{locale}", methods={"GET"}, name="api_map_cases")
     */
    public function getMapCases(Request $request, string $locale): JsonResponse
    {
        $submittedToken = $request->query->get('token');

        if (!$this->isCsrfTokenValid('map-cases', $submittedToken)) {
            return $this->jsonAPI->makeHTTPJSONResponse(Response::HTTP_UNAUTHORIZED);
        }

        try {
            $cache = $this->cacheFactory->create();
            $cacheKey = 'map_cases-' . $locale;
            $frontendCases = $cache->get($cacheKey, function(ItemInterface $cacheItem) use($locale) {
                $cacheItem->expiresAfter(DateInterval::createFromDateString('5 hours'));

                return $this->getItems($locale);
            });

            return $this->json(['items' => $frontendCases]);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage() . ' ' . $exception->getTraceAsString());
            return $this->jsonAPI->makeHTTPJSONResponse(Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (InvalidArgumentException $exception) {
            $this->logger->error($exception->getMessage() . ' ' . $exception->getTraceAsString());
            return $this->jsonAPI->makeHTTPJSONResponse(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}