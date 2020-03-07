<?php

namespace App\Controller\Admin;

use App\Entity\MapCase;
use App\Entity\News;
use App\Enum\Admin\FlashTypes;
use App\Form\Admin\NewsForm;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use \Exception;
use App\Repository\MapCaseRepository;
use App\Form\Admin\MapCaseEditForm;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 * @Route("/admin")
 */
class EditController extends AbstractAdminController
{
    /**
     * @var MapCaseRepository
     */
    private $mapCaseRepository;

    /**
     * @var string
     */
    protected $pswChangeSessionKey;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var NewsRepository
     */
    private $newsRepository;

    public function __construct(
        MapCaseRepository $mapCaseRepository,
        string $pswChangeSessionKey,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        NewsRepository $newsRepository
    )
    {
        $this->mapCaseRepository = $mapCaseRepository;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->newsRepository = $newsRepository;

        parent::__construct($pswChangeSessionKey);
    }

    /**
     * @param $entity
     * @return RedirectResponse
     * @throws Exception
     */
    private function submit($entity)
    {
        try {
            $this->entityManager->flush();
            $this->addFlash(FlashTypes::OK, $entity->getNameEN().' has been updated!');

            return $this->redirectToRoute('admin_entity_list', ['entityName' => $entity::URL_PARAM_NAME]);
        } catch (Exception $exception) {
            throw $exception;
        }

    }

    private function getEntityAndForm(string $entityName, string $id): array
    {
        switch ($entityName) {
            case MapCase::URL_PARAM_NAME:
                $entity = $this->mapCaseRepository->find($id);
                $form = $this->formFactory->create(MapCaseEditForm::class, $entity);
                break;
            case News::URL_PARAM_NAME:
                $entity = $this->newsRepository->find($id);
                $form = $this->formFactory->create(NewsForm::class, $entity);
                break;
            default:
                throw $this->createNotFoundException();
        }

        return [$entity, $form];
    }

    /**
     * @Route("/edit/{entityName}/{id}", name="admin_entity_edit")
     * @param Request $request
     * @param string $entityName
     * @param string $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function edit(Request $request, string $entityName, string $id)
    {
        if ($this->checkForPasswordChangeSession($request)) {
            return $this->redirectToPasswordChange();
        }

        list($entity, $form) = $this->getEntityAndForm($entityName, $id);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                return $this->submit($entity);
            } catch (Exception $exception) {
                $this->addFlash(FlashTypes::ERROR, $exception->getMessage());
            }
        }

        return $this->render('admin/entity/edit.html.twig', [
            'form' => $form->createView(),
            'entity' => $entity,
            'entityDisplayName' => $entity::DISPLAY_NAME
        ]);
    }
}