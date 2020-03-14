<?php

namespace App\Controller\Admin;

use App\Enum\Admin\FlashTypes;
use App\Service\EntityService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 * @Route("/admin")
 */
class ListController extends AbstractAdminController
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
     * @Route("/list/{entityName}/", name="admin_entity_list")
     * @param Request $request
     * @param string $entityName
     * @return Response
     */
    public function edit(Request $request, string $entityName): Response
    {
        if ($this->checkForPasswordChangeSession($request)) {
            return $this->redirectToPasswordChange();
        }

        try {
            list($items, $entityDisplayName) = $this->entityService->getItemsAndDisplayName($entityName);

            return $this->render('admin/entity/list.html.twig', [
                'items' => $items,
                'entityDisplayName' => $entityDisplayName
            ]);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage().' | '.$exception->getTraceAsString());
            $this->addFlash(FlashTypes::ERROR, 'An error has happened while listing the items');

            throw new NotFoundHttpException();
        }
    }
}