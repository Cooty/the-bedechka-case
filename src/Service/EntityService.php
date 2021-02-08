<?php

namespace App\Service;

use App\Entity\CrewMember;
use App\Form\Admin\CrewMemberEditType;
use App\Form\Admin\CrewMemberType;
use App\Repository\CrewMemberRepository;
use App\Entity\MapCase;
use App\Entity\News;
use App\Entity\Screening;
use App\Form\Admin\MapCaseEditType;
use App\Form\Admin\MapCaseType;
use App\Form\Admin\NewsType;
use App\Form\Admin\ScreeningType;
use App\Repository\MapCaseRepository;
use App\Repository\NewsRepository;
use App\Repository\ScreeningRepository;
use App\Service\Admin\AbstractEntityHandler;
use App\Service\Admin\CrewMemberHandler;
use App\Service\Admin\FileUploadService;
use App\Service\Admin\MapCaseHandler;
use App\Service\Admin\NewsHandler;
use App\Service\Admin\ScreeningHandler;
use App\Service\Admin\ScreeningUpdateHandler;
use App\Service\Admin\MapCaseUpdateHandler;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EntityService
{
    /**
     * @var EntityManagerInterface
     */
    public $entityManager;

    /**
     * @var MapCaseRepository
     */
    private $mapCaseRepository;

    /**
     * @var NewsRepository
     */
    private $newsRepository;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var ScreeningRepository
     */
    private $screeningRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CrewMemberRepository
     */
    private $crewMemberRepository;

    /**
     * @var FileUploadService
     */
    private $fileUploadService;

    public function __construct(
        MapCaseRepository $mapCaseRepository,
        NewsRepository $newsRepository,
        ScreeningRepository $screeningRepository,
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        LoggerInterface $logger,
        CrewMemberRepository $crewMemberRepository,
        FileUploadService $fileUploadService
    )
    {
        $this->mapCaseRepository = $mapCaseRepository;
        $this->entityManager = $entityManager;
        $this->newsRepository = $newsRepository;
        $this->formFactory = $formFactory;
        $this->screeningRepository = $screeningRepository;
        $this->logger = $logger;
        $this->crewMemberRepository = $crewMemberRepository;
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * @param string $entityName
     * @param string $id
     * @return object|null
     */
    public function getEntityById(string $entityName, string $id)
    {
        switch ($entityName) {
            case MapCase::URL_PARAM_NAME:
                return $this->mapCaseRepository->find($id);
            case News::URL_PARAM_NAME:
                return $this->newsRepository->find($id);
            case Screening::URL_PARAM_NAME:
                return $this->screeningRepository->find($id);
            case CrewMember::URL_PARAM_NAME:
                return $this->crewMemberRepository->find($id);
            default:
                return null;
        }
    }

    public function getItemsAndDisplayName(string $entityName): array
    {
        switch ($entityName) {
            case MapCase::URL_PARAM_NAME:
                $items = $this->mapCaseRepository->findActive();
                $entityDisplayName = MapCase::DISPLAY_NAME;
                break;
            case News::URL_PARAM_NAME:
                $items = $this->newsRepository->findActive();
                $entityDisplayName = News::DISPLAY_NAME;
                break;
            case Screening::URL_PARAM_NAME:
                $items = $this->screeningRepository->findActive();
                $entityDisplayName = Screening::DISPLAY_NAME;
                break;
            case CrewMember::URL_PARAM_NAME:
                $items = $this->crewMemberRepository->findActive();
                $entityDisplayName = CrewMember::DISPLAY_NAME;
                break;
            default:
                throw new NotFoundHttpException();
        }

        return [$items, $entityDisplayName];
    }

    public function getSubmitParamsForExisting(string $entityName, string $id): array
    {
        switch ($entityName) {
            case MapCase::URL_PARAM_NAME:
                $entity = $this->mapCaseRepository->find($id);
                $form = $this->formFactory->create(MapCaseEditType::class, $entity);
                $handler = new MapCaseUpdateHandler($entity, $form, $this->fileUploadService);
                $formHandler = null;
                break;
            case News::URL_PARAM_NAME:
                $entity = $this->newsRepository->find($id);
                $form = $this->formFactory->create(NewsType::class, $entity);
                $handler = new NewsHandler($entity, $form, $this->fileUploadService);
                $formHandler = null;
                break;
            case Screening::URL_PARAM_NAME:
                $entity = $this->screeningRepository->find($id);
                $form = $this->formFactory->create(ScreeningType::class, $entity);
                $handler = new ScreeningHandler($entity, $form, $this->fileUploadService);
                $formHandler = new ScreeningUpdateHandler($entity, $form);
                break;
            case CrewMember::URL_PARAM_NAME:
                $entity = $this->crewMemberRepository->find($id);
                $form = $this->formFactory->create(CrewMemberEditType::class, $entity);
                $handler = new CrewMemberHandler($entity, $form, $this->fileUploadService);
                $formHandler = null;
                break;
            default:
                throw new NotFoundHttpException();
        }

        if($formHandler) {
            $form = $formHandler->getForm();
        }

        return [$entity, $form, $handler];
    }

    public function getSubmitParams(string $entityName): array
    {
        switch ($entityName) {
            case MapCase::URL_PARAM_NAME:
                $entity = new MapCase();
                $form = $this->formFactory->create(MapCaseType::class, $entity);
                $handler = new MapCaseHandler($entity, $form, $this->fileUploadService);
                break;
            case News::URL_PARAM_NAME:
                $entity = new News();
                $form = $this->formFactory->create(NewsType::class, $entity);
                $handler = new NewsHandler($entity, $form, $this->fileUploadService);
                break;
            case Screening::URL_PARAM_NAME:
                $entity = new Screening();
                $form = $this->formFactory->create(ScreeningType::class, $entity);
                $handler = new ScreeningHandler($entity, $form, $this->fileUploadService);
                break;
            case CrewMember::URL_PARAM_NAME:
                $entity = new CrewMember();
                $form = $this->formFactory->create(CrewMemberType::class, $entity);
                $handler = new CrewMemberHandler($entity, $form, $this->fileUploadService);
                break;
            default:
                throw new NotFoundHttpException();
        }

        return [$entity, $form, $handler];
    }

    /**
     * @param AbstractEntityHandler|null $handler
     * @param object|null $entity
     * @return object
     * @throws Exception
     */
    public function create(?AbstractEntityHandler $handler, $entity)
    {
        try {
            if ($handler) {
                $entity = $handler->getEntity();
            }

            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            return $entity;

        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage() . ' | ' . $exception->getTraceAsString());
            throw $exception;
        }
    }

    /**
     * @param AbstractEntityHandler|null $handler
     * @param $entity
     * @return object
     * @throws Exception
     */
    public function update(?AbstractEntityHandler $handler, $entity)
    {
        try {
            if ($handler) {
                $entity = $handler->getEntity();
            }

            $this->entityManager->flush();

            return $entity;
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage() . ' | ' . $exception->getTraceAsString());
            throw $exception;
        }
    }
}