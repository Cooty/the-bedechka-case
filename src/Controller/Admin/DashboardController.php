<?php

namespace App\Controller\Admin;

use App\Service\Admin\RedirectService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 * @Route("/admin")
 */
class DashboardController extends AbstractAdminController
{
    /**
     * @Route("/", name="admin_dashboard")
     * @param Request $request
     * @param RedirectService $redirectService
     * @return Response
     */
    public function login(
        Request $request,
        RedirectService $redirectService
    ): Response
    {
        if($this->checkForPasswordChangeSession($request)) {
            return $this->redirectToPasswordChange();
        }

        if($redirectService->validateImplicitLogout($request->cookies)) {
            return $redirectService->redirectAfterImplicitLogout($request->cookies);
        }

        return $this->render('admin/dashboard/index.html.twig');
    }
}