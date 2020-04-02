<?php

namespace App\Controller\Admin;

use App\Enum\Admin\FlashTypes;
use App\Service\EntityService;
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
class AddController extends AbstractAdminController
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EntityService
     */
    private $entityService;

    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct(
        string $pswChangeSessionKey,
        LoggerInterface $logger,
        EntityService $entityService,
        CacheInterface $cache
    )
    {
        $this->logger = $logger;
        $this->entityService = $entityService;

        parent::__construct($pswChangeSessionKey);
        $this->cache = $cache;
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
                $entity = $this->entityService->create($handler, $entity, $params);

                $this->addFlash(FlashTypes::OK, 'The new ' . $entity::DISPLAY_NAME . ' has been created!');
                $this->cache->clear();

                return $this->redirectToRoute('admin_entity_list', ['entityName' => $entity::URL_PARAM_NAME]);
            } catch (Exception $exception) {
                $this->logger->error($exception->getMessage().' | '.$exception->getTraceAsString());
                $this->addFlash(FlashTypes::ERROR, 'An error has happened while saving!');
            }
        }

        return $this->render('admin/entity/add.html.twig', [
            'form' => $form->createView(),
            'entityDisplayName' => $entity::DISPLAY_NAME
        ]);
    }
}