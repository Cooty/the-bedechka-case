<?php

namespace App\Controller\Admin;

use App\Enum\Admin\FlashTypes;
use App\Service\EntityService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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

    public function __construct(
        string $pswChangeSessionKey,
        EntityManagerInterface $entityManager,
        EntityService $entityService
    )
    {
        $this->entityManager = $entityManager;
        $this->entityService = $entityService;

        parent::__construct($pswChangeSessionKey);
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

        list($entity, $form) = $this->entityService->getEntityAndForm($entityName, $id);

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

    /**
     * @param $entity
     * @return RedirectResponse
     * @throws Exception
     */
    private function submit($entity)
    {
        try {
            $this->entityManager->flush();
            $name = method_exists($entity, 'getNameEN') ? $entity->getNameEN() : $entity->getTitle();

            $this->addFlash(FlashTypes::OK, $name . ' has been updated!');

            return $this->redirectToRoute('admin_entity_list', ['entityName' => $entity::URL_PARAM_NAME]);
        } catch (Exception $exception) {
            throw $exception;
        }

    }
}