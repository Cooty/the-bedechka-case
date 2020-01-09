<?php

namespace App\Controller\Admin\Security;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

/**
 * @Route("/admin")
 */
class Login
{
    /**
     * @var Environment
     */
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @Route("/login", name="admin_security_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     * @throws Exception
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return new Response($this->twig->render(
            'admin/security/login.html.twig',
            [
                'last_email' => $authenticationUtils->getLastUsername(),
                'error' => $authenticationUtils->getLastAuthenticationError(),
            ]
        ));
    }
}