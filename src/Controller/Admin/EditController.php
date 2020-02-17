<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use \Exception;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 * @Route("/admin")
 */
class EditController extends AbstractAdminController
{
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

        return $this->render('admin/entity/edit.html.twig', [
            'id' => $id,
            'entityDisplayName' => $entityName
        ]);
    }
}