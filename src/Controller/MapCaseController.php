<?php

namespace App\Controller;

use App\Repository\MapCaseRepository;
use App\Service\TransportService;
use DateInterval;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @param Request $request
     * @param string $locale
     * @return JsonResponse
     * @throws InvalidArgumentException
     * @Route("/map-cases/{locale}", methods={"GET"}, name="api_map_cases")
     */
    public function getMapCases(Request $request, string $locale): JsonResponse
    {
        $submittedToken = $request->query->get('token');

        if(!$this->isCsrfTokenValid('map-cases', $submittedToken)) {
            return $this->json(
                ['message' => Response::$statusTexts[Response::HTTP_UNAUTHORIZED]],
                Response::HTTP_UNAUTHORIZED);
        }

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