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
     * @var string
     */
    private $mapImagesDirectory;

    /**
     * @var ScreeningRepository
     */
    private $screeningRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $crewMemberImagesDirectory;

    /**
     * @var CrewMemberRepository
     */
    private $crewMemberRepository;

    /**
     * @var string
     */
    private $publicDirectoryPath;

    /**
     * @var FileUploadService
     */
    private $fileUploadService;

    /**
     * @var string
     */
    private $newsLogoDirectory;

    public function __construct(
        MapCaseRepository $mapCaseRepository,
        NewsRepository $newsRepository,
        ScreeningRepository $screeningRepository,
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        string $mapImagesDirectory,
        LoggerInterface $logger,
        string $crewMemberImagesDirectory,
        CrewMemberRepository $crewMemberRepository,
        string $publicDirectoryPath,
        FileUploadService $fileUploadService,
        string $newsLogoDirectory
    )
    {
        $this->mapCaseRepository = $mapCaseRepository;
        $this->entityManager = $entityManager;
        $this->newsRepository = $newsRepository;
        $this->formFactory = $formFactory;
        $this->mapImagesDirectory = $mapImagesDirectory;
        $this->screeningRepository = $screeningRepository;
        $this->logger = $logger;
        $this->crewMemberImagesDirectory = $crewMemberImagesDirectory;
        $this->crewMemberRepository = $crewMemberRepository;
        $this->publicDirectoryPath = $publicDirectoryPath;
        $this->fileUploadService = $fileUploadService;
        $this->newsLogoDirectory = $newsLogoDirectory;
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
                $params = ['upload_path' => $this->mapImagesDirectory, 'public_path' => $this->publicDirectoryPath];
                $handler = new MapCaseUpdateHandler($entity, $form, $this->fileUploadService);
                $formHandler = null;
                break;
            case News::URL_PARAM_NAME:
                $entity = $this->newsRepository->find($id);
                $form = $this->formFactory->create(NewsType::class, $entity);
                $params = ['upload_path' => $this->newsLogoDirectory, 'public_path' => $this->publicDirectoryPath];
                $handler = new NewsHandler($entity, $form, $this->fileUploadService);
                $formHandler = null;
                break;
            case Screening::URL_PARAM_NAME:
                $entity = $this->screeningRepository->find($id);
                $form = $this->formFactory->create(ScreeningType::class, $entity);
                $params = [];
                $handler = new ScreeningHandler($entity, $form);
                $formHandler = new ScreeningUpdateHandler($entity, $form);
                break;
            case CrewMember::URL_PARAM_NAME:
                $entity = $this->crewMemberRepository->find($id);
                $form = $this->formFactory->create(CrewMemberEditType::class, $entity);
                $params = ['upload_path' => $this->crewMemberImagesDirectory, 'public_path' => $this->publicDirectoryPath];
                $handler = new CrewMemberHandler($entity, $form, $this->fileUploadService);
                $formHandler = null;
                break;
            default:
                throw new NotFoundHttpException();
        }

        if($formHandler) {
            $form = $formHandler->getForm();
        }

        return [$entity, $form, $params, $handler];
    }

    public function getSubmitParams(string $entityName): array
    {
        switch ($entityName) {
            case MapCase::URL_PARAM_NAME:
                $entity = new MapCase();
                $form = $this->formFactory->create(MapCaseType::class, $entity);
                $params = ['upload_path' => $this->mapImagesDirectory, 'public_path' => $this->publicDirectoryPath];
                $handler = new MapCaseHandler($entity, $form, $this->fileUploadService);
                break;
            case News::URL_PARAM_NAME:
                $entity = new News();
                $form = $this->formFactory->create(NewsType::class, $entity);
                $params = ['upload_path' => $this->newsLogoDirectory, 'public_path' => $this->publicDirectoryPath];
                $handler = new NewsHandler($entity, $form, $this->fileUploadService);
                break;
            case Screening::URL_PARAM_NAME:
                $entity = new Screening();
                $form = $this->formFactory->create(ScreeningType::class, $entity);
                $params = [];
                $handler = new ScreeningHandler($entity, $form);
                break;
            case CrewMember::URL_PARAM_NAME:
                $entity = new CrewMember();
                $form = $this->formFactory->create(CrewMemberType::class, $entity);
                $params = ['upload_path' => $this->crewMemberImagesDirectory, 'public_path' => $this->publicDirectoryPath];
                $handler = new CrewMemberHandler($entity, $form, $this->fileUploadService);
                break;
            default:
                throw new NotFoundHttpException();
        }

        return [$entity, $form, $params, $handler];
    }

    /**
     * @param AbstractEntityHandler|null $handler
     * @param object|null $entity
     * @param array $params
     * @return object
     * @throws Exception
     */
    public function create(?AbstractEntityHandler $handler, $entity, array $params = [])
    {
        try {
            if ($handler) {
                $entity = $handler->getEntity($params);
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
     * @param array $params
     * @return object
     * @throws Exception
     */
    public function update(?AbstractEntityHandler $handler, $entity, array $params = [])
    {
        try {
            if ($handler) {
                $entity = $handler->getEntity($params);
            }

            $this->entityManager->flush();

            return $entity;
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage() . ' | ' . $exception->getTraceAsString());
            throw $exception;
        }
    }
}