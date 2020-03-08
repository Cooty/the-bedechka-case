<?php

namespace App\Service;

use App\Entity\MapCase;
use App\Entity\News;
use App\Form\Admin\MapCaseEditForm;
use App\Form\Admin\MapCaseForm;
use App\Form\Admin\NewsForm;
use App\Repository\MapCaseRepository;
use App\Repository\NewsRepository;
use App\Service\Admin\AbstractEntityHandler;
use App\Service\Admin\MapCaseHandler;
use App\Service\Admin\NewsHandler;
use Doctrine\ORM\EntityManagerInterface;
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

    public function __construct(
        MapCaseRepository $mapCaseRepository,
        NewsRepository $newsRepository,
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        string $mapImagesDirectory
    )
    {
        $this->mapCaseRepository = $mapCaseRepository;
        $this->entityManager = $entityManager;
        $this->newsRepository = $newsRepository;
        $this->formFactory = $formFactory;
        $this->mapImagesDirectory = $mapImagesDirectory;
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
                $form = $this->formFactory->create(MapCaseEditForm::class, $entity);
                $params = [];
                $handler = null;
                break;
            case News::URL_PARAM_NAME:
                $entity = $this->newsRepository->find($id);
                $form = $this->formFactory->create(NewsForm::class, $entity);
                $params = [];
                $handler = new NewsHandler($entity, $form);
                break;
            default:
                throw new NotFoundHttpException();
        }

        return [$entity, $form, $params, $handler];
    }

    public function getSubmitParams(string $entityName): array
    {
        switch ($entityName) {
            case MapCase::URL_PARAM_NAME:
                $entity = new MapCase();
                $form = $this->formFactory->create(MapCaseForm::class, $entity);
                $params = ['upload_path' => $this->mapImagesDirectory];
                $handler = new MapCaseHandler($entity, $form);
                break;
            case News::URL_PARAM_NAME:
                $entity = new News();
                $form = $this->formFactory->create(NewsForm::class, $entity);
                $params = [];
                $handler = new NewsHandler($entity, $form);
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
            throw $exception;
        }
    }
}