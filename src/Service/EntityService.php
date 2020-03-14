<?php

namespace App\Service;

use App\Entity\MapCase;
use App\Entity\News;
use App\Form\Admin\MapCaseEditType;
use App\Form\Admin\MapCaseType;
use App\Form\Admin\NewsType;
use App\Form\Admin\ScreeningType;
use App\Service\Admin\ScreeningHandler;
use App\Repository\MapCaseRepository;
use App\Repository\NewsRepository;
use App\Entity\Screening;
use App\Service\Admin\ScreeningUpdateHandler;
use App\Repository\ScreeningRepository;
use App\Service\Admin\AbstractEntityHandler;
use App\Service\Admin\MapCaseHandler;
use App\Service\Admin\NewsHandler;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use \Exception;

class EntityService
{
    /**
     * @var MapCaseRepository
     */
    private $mapCaseRepository;

    /**
     * @var EntityManagerInterface
     */
    public $entityManager;

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

    public function __construct(
        MapCaseRepository $mapCaseRepository,
        NewsRepository $newsRepository,
        ScreeningRepository $screeningRepository,
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        string $mapImagesDirectory,
        LoggerInterface $logger
    )
    {
        $this->mapCaseRepository = $mapCaseRepository;
        $this->entityManager = $entityManager;
        $this->newsRepository = $newsRepository;
        $this->formFactory = $formFactory;
        $this->mapImagesDirectory = $mapImagesDirectory;
        $this->screeningRepository = $screeningRepository;
        $this->logger = $logger;
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
                $params = [];
                $handler = null;
                $formHandler = null;
                break;
            case News::URL_PARAM_NAME:
                $entity = $this->newsRepository->find($id);
                $form = $this->formFactory->create(NewsType::class, $entity);
                $params = [];
                $handler = new NewsHandler($entity, $form);
                $formHandler = null;
                break;
            case Screening::URL_PARAM_NAME:
                $entity = $this->screeningRepository->find($id);
                $form = $this->formFactory->create(ScreeningType::class, $entity);
                $params = [];
                $handler = new ScreeningHandler($entity, $form);
                $formHandler = new ScreeningUpdateHandler($entity, $form);
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
                $params = ['upload_path' => $this->mapImagesDirectory];
                $handler = new MapCaseHandler($entity, $form);
                break;
            case News::URL_PARAM_NAME:
                $entity = new News();
                $form = $this->formFactory->create(NewsType::class, $entity);
                $params = [];
                $handler = new NewsHandler($entity, $form);
                break;
            case Screening::URL_PARAM_NAME:
                $entity = new Screening();
                $form = $this->formFactory->create(ScreeningType::class, $entity);
                $params = [];
                $handler = new ScreeningHandler($entity, $form);
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
            if($handler) {
                $entity = $handler->getEntity($params);
            }

            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            return $entity;

        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage().' | '.$exception->getTraceAsString());
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
            if($handler) {
                $entity = $handler->getEntity($params);
            }

            $this->entityManager->flush();

            return $entity;
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage().' | '.$exception->getTraceAsString());
            throw $exception;
        }
    }
}