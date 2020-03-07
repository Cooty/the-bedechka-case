<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Service\EntityService;
use App\Service\JSONAPIService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/admin")
 */
class DeleteController
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

    public function __construct(
        Security $security,
        EntityService $entityService,
        JSONAPIService $jsonAPI
    )
    {
        $this->security = $security;
        $this->jsonAPI = $jsonAPI;
        $this->entityService = $entityService;
    }

    /**
     * @Route("/delete/{entityName}", methods="POST", name="admin_entity_delete")
     * @param Request $request
     * @param string $entityName
     * @return JsonResponse
     */
    public function delete(Request $request, string $entityName): JsonResponse
    {
        if(!$this->security->isGranted(User::ROLE_ADMIN)) {
            return $this->jsonAPI->makeHTTPJSONResponse(Response::HTTP_FORBIDDEN);
        }

        if(!$this->jsonAPI->requestHasIDField($request)) {
            return $this->jsonAPI->makeHTTPJSONResponse(Response::HTTP_BAD_REQUEST);
        }

        $id = $this->jsonAPI->getIDFromRequestBody($request);

        $entity = $this->entityService->getEntityById($entityName, $id);

        if(empty($entity)) {
            return $this->jsonAPI->makeHTTPJSONResponse(Response::HTTP_NOT_FOUND);
        }

        $this->entityService->entityManager->remove($entity);
        $this->entityService->entityManager->flush();

        return $this->jsonAPI->makeHTTPJSONResponse(Response::HTTP_OK);
    }
}