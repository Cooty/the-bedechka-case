<?php

namespace App\Controller;

use App\Entity\Transport\MapCaseFrontend;
use App\Enum\Cache;
use App\Repository\MapCaseRepository;
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
use Symfony\Contracts\Cache\CacheInterface;
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
     * @var CacheInterface
     */
    private $appCache;

    public function __construct(
        MapCaseRepository $mapCaseRepository,
        TransportService $transport,
        JSONAPIService $jsonAPI,
        LoggerInterface $logger,
        CacheInterface $appCache
    )
    {
        $this->mapCaseRepository = $mapCaseRepository;
        $this->transport = $transport;
        $this->jsonAPI = $jsonAPI;
        $this->logger = $logger;
        $this->appCache = $appCache;
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
        try {
            $cacheKey = 'map_cases-' . $locale;
            $frontendCases = $this->appCache->get($cacheKey, function (ItemInterface $cacheItem) use ($locale) {
                $cacheItem->expiresAfter(DateInterval::createFromDateString(Cache::API_RESPONSE_EXPIRATION));

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