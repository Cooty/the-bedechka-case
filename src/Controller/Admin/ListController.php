<?php

namespace App\Controller\Admin;

use App\Entity\MapCase;
use App\Repository\MapCaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Traits\Admin\Security\PasswordChange;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

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
     * @var MapCaseRepository
     */
    private $casesRepository;

    public function __construct(
        string $pswChangeSessionKey,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        MapCaseRepository $casesRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;

        parent::__construct($pswChangeSessionKey);
        $this->casesRepository = $casesRepository;
    }

    /**
     * @Route("/list/{entityName}/", name="admin_entity_list")
     * @param Request $request
     * @param string $entityName
     * @return Response
     */
    public function edit(Request $request, string $entityName): Response
    {
        if($this->checkForPasswordChangeSession($request)) {
            return $this->redirectToPasswordChange();
        }

        if($entityName === 'cases') {
            $items = $this->casesRepository->findActive();
            $entityDisplayName = MapCase::DISPLAY_NAME;
        }

        return $this->render('admin/entity/list.html.twig', [
            'items' => $items,
            'entityDisplayName' => $entityDisplayName
        ]);
    }
}