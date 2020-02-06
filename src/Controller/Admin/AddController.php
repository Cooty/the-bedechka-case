<?php

namespace App\Controller\Admin;

use App\Entity\MapCase;
use App\Form\Admin\MapCaseForm;
use App\Handler\Admin\AbstractEntityHandler;
use App\Handler\Admin\MapCaseHandler;
use App\Traits\Admin\Security\PasswordChange;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use \Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 * @Route("/admin")
 */
class AddController extends AbstractController
{
    use PasswordChange;

    /**
     * @var string
     */
    private $pswChangeSessionKey;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

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
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    )
    {
        $this->pswChangeSessionKey = $pswChangeSessionKey;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * @param AbstractEntityHandler $handler
     * @param array $params
     * @param string $entityName
     * @return RedirectResponse
     * @throws Exception
     */
    private function submit(AbstractEntityHandler $handler, array $params, string $entityName)
    {
        try {
            $entity = $handler->getEntity($params);
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
            $this->addFlash('success', 'The new '.$entity::DISPLAY_NAME .' has been created!');

            return $this->redirectToRoute('admin_entity_list', ['entityName' => $entityName]);
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
    public function edit(Request $request, string $entityName)
    {
        // TODO: Figure out a way where we don't have to copy-paste this logic to all admin controllers! Events?
        if ($this->checkForPasswordChangeSession($request)) {
            return $this->redirectToPasswordChange();
        }

        if ($entityName === MapCase::URL_PARAM_NAME) {
            $entity = new MapCase();
            $form = $this->formFactory->create(MapCaseForm::class, $entity);
            $params = ['upload_path' => $this->getParameter('map_images_directory')];
            $handler = new MapCaseHandler($entity, $form);
        } else {
            throw $this->createNotFoundException();
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                return $this->submit($handler, $params, $entityName);
            } catch (Exception $exception) {
                $this->addFlash('danger', $exception->getMessage());
            }

        }

        return $this->render('admin/entity/add.html.twig', [
            'form' => $form->createView(),
            'entityDisplayName' => $entity::DISPLAY_NAME
        ]);
    }
}