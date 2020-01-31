<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
class ListController extends AbstractController
{
    use PasswordChange;

    /**
     * @var string
     */
    private $pswChangeSessionKey;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        string $pswChangeSessionKey,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    )
    {
        $this->pswChangeSessionKey = $pswChangeSessionKey;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
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
            $displayName = 'map cases';
        }

        return $this->render('admin/entity/list.html.twig', [
            'entityDisplayName' => $displayName
        ]);
    }
}