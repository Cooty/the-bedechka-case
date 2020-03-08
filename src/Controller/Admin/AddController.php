<?php

namespace App\Controller\Admin;

use App\Enum\Admin\FlashTypes;
use App\Service\Admin\AbstractEntityHandler;
use App\Service\EntityService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 * @Route("/admin")
 */
class AddController extends AbstractAdminController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var EntityService
     */
    private $entityService;

    public function __construct(
        string $pswChangeSessionKey,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        EntityService $entityService
    )
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->entityService = $entityService;

        parent::__construct($pswChangeSessionKey);
    }

    /**
     * @param AbstractEntityHandler|null $handler
     * @param object|null $entity
     * @param array $params
     * @return RedirectResponse
     * @throws Exception
     */
    private function submit(?AbstractEntityHandler $handler, $entity, array $params = [])
    {
        try {
            if ($handler) {
                $entity = $handler->getEntity($params);
            }

            $this->entityManager->persist($entity);
            $this->entityManager->flush();
            $this->addFlash(FlashTypes::OK, 'The new ' . $entity::DISPLAY_NAME . ' has been created!');

            return $this->redirectToRoute('admin_entity_list', ['entityName' => $entity::URL_PARAM_NAME]);
        } catch (Exception $exception) {
            throw $exception;
        }
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

        list($entity, $form, $params, $handler) = $this->entityService->getSubmitParams($entityName);

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