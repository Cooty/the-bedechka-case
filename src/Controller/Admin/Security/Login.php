<?php

namespace App\Controller\Admin\Security;

use App\Traits\Admin\Security\PasswordChange;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin")
 */
class Login extends AbstractController
{
    use PasswordChange;

    /**
     * @var string
     */
    private $pswChangeSessionKey;

    public function __construct(string $pswChangeSessionKey)
    {
        $this->pswChangeSessionKey = $pswChangeSessionKey;
    }

    /**
     * @Route("/login", name="admin_security_login")
     * @param AuthenticationUtils $authenticationUtils
     * @param Request $request
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        if(empty($this->getUser())) {
            return $this->render(
                'admin/security/login.html.twig', [
                    'last_email' => $authenticationUtils->getLastUsername(),
                    'error' => $authenticationUtils->getLastAuthenticationError(),
                ]);
        } else {
            if($this->checkForPasswordChangeSession($request)) {
                return $this->redirectToPasswordChange();
            } else {
                return $this->redirectToRoute('admin_dashboard');
            }
        }
    }
}