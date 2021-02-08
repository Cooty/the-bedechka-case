<?php

namespace App\Controller\Admin;

use App\Enum\Admin\FlashTypes;
use App\Service\EntityService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 * @Route("/admin")
 */
class EditController extends AbstractAdminController
{
    /**
     * @var string
     */
    protected $pswChangeSessionKey;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EntityService
     */
    private $entityService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct(
        string $pswChangeSessionKey,
        EntityManagerInterface $entityManager,
        EntityService $entityService,
        LoggerInterface $logger,
        CacheInterface $cache
    )
    {
        $this->entityManager = $entityManager;
        $this->entityService = $entityService;

        parent::__construct($pswChangeSessionKey);
        $this->logger = $logger;
        $this->cache = $cache;
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

        list($entity, $form, $handler) = $this->entityService->getSubmitParamsForExisting($entityName, $id);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entity = $this->entityService->update($handler, $entity);

                $name = method_exists($entity, 'getNameEN') ? $entity->getNameEN() : $entity->getTitle();
                $this->addFlash(FlashTypes::OK, $name . ' has been updated!');
                $this->cache->clear();

                return $this->redirectToRoute('admin_entity_list', ['entityName' => $entity::URL_PARAM_NAME]);
            } catch (Exception $exception) {
                $this->logger->error($exception->getMessage() . ' | ' . $exception->getTraceAsString());
                $this->addFlash(FlashTypes::ERROR, 'An error has happened while saving');
            }
        }

        return $this->render('admin/entity/edit.html.twig', [
            'form' => $form->createView(),
            'entity' => $entity,
            'entityDisplayName' => $entity::DISPLAY_NAME
        ]);
    }
}