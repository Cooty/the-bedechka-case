<?php

namespace App\Controller;

use App\Repository\MapCaseRepository;
use App\Service\TransportService;
use DateInterval;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
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

    public function __construct(
        MapCaseRepository $mapCaseRepository,
        TransportService $transport
    )
    {
        $this->mapCaseRepository = $mapCaseRepository;
        $this->transport = $transport;
    }

    /**
     * @param string $locale
     * @return JsonResponse
     * @Route("/map-cases/{locale}", methods={"GET"}, name="api_map_cases")
     * @throws InvalidArgumentException
     */
    public function getMapCases(string $locale): JsonResponse
    {
        $cases = $this->mapCaseRepository->findActive();
        $cache = new FilesystemAdapter();
        $cacheKey = 'map_cases-'.$locale;
        $frontendCases = $cache->get($cacheKey, function(ItemInterface $item) use ($cases, $locale) {
            $item->expiresAfter(DateInterval::createFromDateString('1 hour'));

            $frontendCases = $this->transport->makeMapCasesFrontend($cases, $locale);

            return $frontendCases;
        });

        $data = ['locations'=> $frontendCases];

        return $this->json($data);
    }
}