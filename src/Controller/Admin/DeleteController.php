<?php

namespace App\Controller\Admin;

use App\Entity\MapCase;
use App\Entity\User;
use App\Enum\Admin\ContentTypes;
use App\Repository\MapCaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/admin")
 */
class DeleteController extends AbstractController
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var MapCaseRepository
     */
    private $mapCaseRepository;

    public function __construct(
        Security $security,
        EntityManagerInterface $entityManager,
        MapCaseRepository $mapCaseRepository
    )
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->mapCaseRepository = $mapCaseRepository;
    }

    protected function isValidRequest(Request $request): bool
    {
        if(strpos($request->headers->get('Content-Type'), ContentTypes::JSON) === false) {
            return false;
        }

        $data = json_decode($request->getContent(), true);

        return array_key_exists('id', $data);
    }

    protected function getIdFromRequestBody(Request $request): string
    {
        $data = json_decode($request->getContent(), true);

        return (string)$data['id'];
    }

    protected function makeResponse(int $statusCode): JsonResponse
    {
        return $this->json([
            'message'=> Response::$statusTexts[$statusCode]],
            $statusCode);
    }

    /**
     * @param string $entityName
     * @param string $id
     * @return object|null
     */
    protected function getEntity(string $entityName, string $id)
    {
        if($entityName === MapCase::URL_PARAM_NAME) {
            return $this->mapCaseRepository->find($id);
        } else {
            return null;
        }
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
            return $this->makeResponse(Response::HTTP_FORBIDDEN);
        }

        if(!$this->isValidRequest($request)) {
            return $this->makeResponse(Response::HTTP_BAD_REQUEST);
        }

        $id = $this->getIdFromRequestBody($request);

        $entity = $this->getEntity($entityName, $id);

        if(empty($entity)) {
            return $this->makeResponse(Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        return $this->makeResponse(Response::HTTP_OK);
    }
}