<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Service\EntityService;
use App\Service\JSONAPIService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Service\Admin\ReorderCrewMembersService;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * @Route("/admin")
 */
class ReorderCrewMembersController
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var JSONAPIService
     */
    private $jsonAPI;

    /**
     * @var EntityService
     */
    private $entityService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ReorderCrewMembersService
     */
    private $reorderCrewMembersService;

    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct(
        Security $security,
        EntityService $entityService,
        JSONAPIService $jsonAPI,
        LoggerInterface $logger,
        ReorderCrewMembersService $reorderCrewMembersService,
        CacheInterface $cache
    )
    {
        $this->security = $security;
        $this->entityService = $entityService;
        $this->jsonAPI = $jsonAPI;
        $this->logger = $logger;
        $this->reorderCrewMembersService = $reorderCrewMembersService;
        $this->cache = $cache;
    }

    /**
     * @Route("/reorder/crew-members", methods="POST", name="admin_crew_member_reorder")
     * @param Request $request
     * @return JsonResponse
     */
    public function reorder(Request $request): JsonResponse
    {
        if(!$this->security->isGranted(User::ROLE_ADMIN)) {
            return $this->jsonAPI->makeHTTPJSONResponse(Response::HTTP_FORBIDDEN);
        }

        $object = json_decode($request->getContent(), true);

        try {
            $this->reorderCrewMembersService->reorder($object);
            $this->cache->clear();
            return $this->jsonAPI->makeHTTPJSONResponse(Response::HTTP_OK);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage().' | '.$exception->getTraceAsString());

            return $this->jsonAPI->makeHTTPJSONResponse(Response::HTTP_INTERNAL_SERVER_ERROR);
        }


    }
}