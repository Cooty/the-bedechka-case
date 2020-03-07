<?php

namespace App\Controller\Admin;

use App\Entity\MapCase;
use App\Form\Admin\MapCaseForm;
use App\Handler\Admin\AbstractEntityHandler;
use App\Handler\Admin\MapCaseHandler;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use \Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Enum\Admin\FlashTypes;
use App\Form\Admin\NewsForm;
use App\Entity\News;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 * @Route("/admin")
 */
class AddController extends AbstractAdminController
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param AbstractEntityHandler|null $handler
     * @param object|null $entity
     * @param array $params
     * @return RedirectResponse
     * @throws Exception
     */
    private function submit(?AbstractEntityHandler $handler, ?$entity, array $params = [])
    {
        try {
            if($handler) {
                $entity = $handler->getEntity($params);
            }

            $this->entityManager->persist($entity);
            $this->entityManager->flush();
            $this->addFlash(FlashTypes::OK, 'The new '.$entity::DISPLAY_NAME .' has been created!');

            return $this->redirectToRoute('admin_entity_list', ['entityName' => $entity::URL_PARAM_NAME]);
        } catch (Exception $exception) {
            throw $exception;
        }

    }

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        string $pswChangeSessionKey,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    )
    {
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->logger = $logger;

        parent::__construct($pswChangeSessionKey);
    }

    private function getSubmitParams(string $entityName): array
    {
        switch ($entityName) {
            case MapCase::URL_PARAM_NAME:
                $entity = new MapCase();
                $form = $this->formFactory->create(MapCaseForm::class, $entity);
                $params = ['upload_path' => $this->getParameter('map_images_directory')];
                $handler = new MapCaseHandler($entity, $form);
                break;
            case News::URL_PARAM_NAME:
                $entity = new News();
                $form = $this->formFactory->create(NewsForm::class, $entity);
                $params = [];
                $handler = null;
                break;
            default:
                throw $this->createNotFoundException();
        }

        return [$entity, $form, $params, $handler];
    }

    /**
     * @Route("/add/{entityName}/", name="admin_entity_add")
     * @param Request $request
     * @param string $entityName
     * @return Response
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function add(Request $request, string $entityName)
    {
        if ($this->checkForPasswordChangeSession($request)) {
            return $this->redirectToPasswordChange();
        }

        list($entity, $form, $params, $handler) = $this->getSubmitParams($entityName);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                return $this->submit($handler, $entity, $params);
            } catch (Exception $exception) {
                $this->addFlash(FlashTypes::ERROR, $exception->getMessage());
            }
        }

        return $this->render('admin/entity/add.html.twig', [
            'form' => $form->createView(),
            'entityDisplayName' => $entity::DISPLAY_NAME
        ]);
    }
}