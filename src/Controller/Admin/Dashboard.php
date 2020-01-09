<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Twig\Environment;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

/**
 * Class NotificationController
 * @package App\Controller\MicroPost
 * @Security("is_granted('ROLE_ADMIN')")
 * @Route("/admin")
 */
class Dashboard
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
     * @Route("/", name="admin_dashboard")
     * @return Response
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function login(): Response
    {
        return new Response($this->twig->render(
            'admin/dashboard/index.html.twig'
        ));
    }
}